<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;

class ProfileController extends Controller
{
    private function audit(User $user, Request $request): void
    {
        $user->profile_updated_at = now();
        $user->profile_updated_by = (string) optional($request->user())->id ?: 'system';
    }

    // Helper for email domain validation (duplicated from AuthController for now or could be a service)
    // Since AuthController has it private, we should probably duplicate it or move it to a trait/service.
    // For now, I will include it here as a private method to keep it self-contained as requested "without damage".
    private const TRUSTED_DOMAINS = [
        'gmail.com',
        'googlemail.com',
        'outlook.com',
        'hotmail.com',
        'live.com',
        'msn.com',
        'yahoo.com',
        'yahoo.co.uk',
        'ymail.com',
        'icloud.com',
        'me.com',
        'mac.com',
        'aol.com',
        'protonmail.com',
        'proton.me',
        'zoho.com',
        'mail.com',
        'gmx.com',
        'gmx.net',
        'fastmail.com',
        'hey.com',
        'tutanota.com',
        'pm.me',
    ];

    private function isValidEmailDomain(string $domain): bool
    {
        $domain = strtolower($domain);
        if (in_array($domain, self::TRUSTED_DOMAINS)) {
            return true;
        }
        $cacheKey = "mx_valid:{$domain}";
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($domain) {
            return checkdnsrr($domain, "MX");
        });
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        Log::info("[ProfileController] Updating profile for User ID: {$user->id}", ['new_name' => $request->name]);
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255',
        ]);
        $user->name = $data['name'];
        $this->audit($user, $request);
        $user->save();
        Log::info("[ProfileController] Profile updated successfully for User ID: {$user->id}");
        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();
        Log::info("[ProfileController] Updating password for User ID: {$user->id}");
        $data = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()->mixedCase()],
        ]);
        if (!Hash::check($data['current_password'], $user->password)) {
            Log::warning("[ProfileController] Password update failed: Incorrect current password for User ID: {$user->id}");
            return response()->json(['message' => 'Current password is incorrect', 'errors' => ['current_password' => ['Incorrect current password']]], 422);
        }
        if (Hash::check($data['password'], $user->password)) {
            Log::warning("[ProfileController] Password update failed: New password same as old for User ID: {$user->id}");
            return response()->json(['message' => 'New password cannot be the same as the old password.', 'errors' => ['password' => ['Choose a new password different from the current one.']]], 422);
        }
        $user->password = Hash::make($data['password']);
        $this->audit($user, $request);
        $user->save();
        Log::info("[ProfileController] Password updated successfully for User ID: {$user->id}");
        return response()->json(['message' => 'Password updated successfully']);
    }

    public function uploadAvatar(Request $request)
    {
        $user = $request->user();
        Log::info("[ProfileController] Uploading avatar for User ID: {$user->id}");
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
        // Delete old avatar if exists
        if ($user->avatar_path) {
            try {
                Storage::disk('public')->delete($user->avatar_path);
            } catch (\Throwable $e) { /* ignore */
            }
        }
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar_path = $path;
        $this->audit($user, $request);
        $user->save();
        Log::info("[ProfileController] Avatar uploaded successfully for User ID: {$user->id}. Path: $path");
        return response()->json(['message' => 'Avatar updated successfully', 'avatar_url' => $user->avatar_url]);
    }

    public function requestEmailChange(Request $request)
    {
        $user = $request->user();
        Log::info("[ProfileController] Requesting email change for User ID: {$user->id}");
        $data = $request->validate([
            'new_email' => 'required|email|max:255|unique:users,email',
            'current_password' => 'required|string',
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            Log::warning("[ProfileController] Email change failed: Incorrect current password for User ID: {$user->id}");
            return response()->json(['message' => 'Current password is incorrect', 'errors' => ['current_password' => ['Incorrect current password']]], 422);
        }

        $newEmail = strtolower(trim($data['new_email']));
        $domain = substr(strrchr($newEmail, '@'), 1);

        if (!$this->isValidEmailDomain($domain)) {
            Log::warning("[ProfileController] Email change failed: Invalid domain $domain for User ID: {$user->id}");
            return response()->json(['message' => 'Invalid email domain', 'errors' => ['new_email' => ['This email domain does not have valid mail servers']]], 422);
        }

        $otp = rand(100000, 999999);
        Log::info("[ProfileController] Email change OTP generated for User ID: {$user->id} -> $newEmail: $otp");
        Cache::put('email_change_otp:' . $user->id . ':' . $newEmail, $otp, now()->addMinutes(15));

        try {
            Mail::raw("Your verification code is: {$otp}", function ($m) use ($newEmail) {
                $m->to($newEmail)->subject('Verify your new email');
            });
            Log::info("[ProfileController] Email change OTP sent to $newEmail");
        } catch (\Exception $e) {
            Log::error("[ProfileController] Email change OTP send failed: " . $e->getMessage());
            return response()->json(['message' => 'Could not send verification email. Please try again later.'], 500);
        }

        return response()->json(['message' => 'Verification code sent to your new email address.']);
    }

    public function confirmEmailChange(Request $request)
    {
        $user = $request->user();
        Log::info("[ProfileController] Confirming email change for User ID: {$user->id}");
        $data = $request->validate([
            'new_email' => 'required|email|max:255|unique:users,email',
            'otp' => 'required|string|size:6',
        ]);
        $newEmail = strtolower(trim($data['new_email']));
        $cacheKey = 'email_change_otp:' . $user->id . ':' . $newEmail;
        $cachedOtp = Cache::get($cacheKey);

        if (!$cachedOtp || $cachedOtp != $data['otp']) {
            Log::warning("[ProfileController] Email change confirmation failed: Invalid or expired OTP for User ID: {$user->id}");
            return response()->json(['message' => 'Invalid or expired verification code.'], 422);
        }

        $user->email = $newEmail;
        if (
            in_array('email_verified_at', $user->getFillable()) ||
            Schema::hasColumn('users', 'email_verified_at')
        ) {
            $user->email_verified_at = null;
        }
        $this->audit($user, $request);
        $user->save();
        Log::info("[ProfileController] Email updated successfully for User ID: {$user->id} to $newEmail");
        Cache::forget($cacheKey);
        return response()->json(['message' => 'Email updated successfully', 'user' => $user]);
    }

    public function deactivateAccount(Request $request)
    {
        $user = $request->user();
        Log::warning("[ProfileController] User deactivating account. User ID: {$user->id}, Email: {$user->email}");
        $user->delete();
        // Assuming Auth::guard('web')->logout() is handled by the caller or we should do it here if using session auth.
        // But since this is API, usually token deletion is enough. AuthController did 'web' logout too.
        // We will keep it for consistency if they are using session auth.
        // Error: 'Auth' not imported explicitly, using \Illuminate\Support\Facades\Auth implied? No, I added import above? No I didn't.
        // Adding import: use Illuminate\Support\Facades\Auth;
        \Illuminate\Support\Facades\Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Log::info("[ProfileController] Account deactivated successfully");
        return response()->json(['message' => 'Account deactivated. You can contact support to restore within 30 days.']);
    }
}
