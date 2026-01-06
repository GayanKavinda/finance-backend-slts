<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Str;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\LoginActivity;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    private function audit(User $user, Request $request): void
    {
        $user->profile_updated_at = now();
        $user->profile_updated_by = (string) optional($request->user())->id ?: 'system';
    }
    // Trusted domains - instant validation, no DNS lookup needed
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

    /**
     * ✅ FAST: Only check if email exists in database
     * Used for real-time validation while typing
     */
    public function checkEmailExists(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = strtolower(trim($request->email));
        $domain = substr(strrchr($email, "@"), 1);

        return response()->json([
            'exists' => User::where('email', $email)->exists(),
            'valid_domain' => $this->isValidEmailDomain($domain)
        ]);
    }

    /**
     * User Registration with full MX validation
     */
    public function register(RegisterRequest $request)
    {
        $email = strtolower(trim($request->email));
        $domain = substr(strrchr($email, "@"), 1);

        // ✅ Full MX validation on submit (with caching)
        if (!$this->isValidEmailDomain($domain)) {
            return response()->json([
                'message' => 'Invalid email domain',
                'errors' => [
                    'email' => ['This email domain does not have valid mail servers']
                ]
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return response()->json([
            'message' => 'User registered and logged in successfully',
            'user' => $user,
        ], 201);
    }

    /**
     * Check if email domain is valid
     * Uses whitelist + cached MX lookups
     */
    private function isValidEmailDomain(string $domain): bool
    {
        $domain = strtolower($domain);

        // ✅ Instant check for trusted providers (Gmail, Outlook, etc.)
        if (in_array($domain, self::TRUSTED_DOMAINS)) {
            return true;
        }

        // ✅ Cache MX results for 24 hours per domain
        $cacheKey = "mx_valid:{$domain}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($domain) {
            return checkdnsrr($domain, "MX");
        });
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = strtolower(trim($credentials['email']));
        $user = User::where('email', $email)->first();

        if ($user && $user->lockout_until && $user->lockout_until->isFuture()) {
            $diff = $user->lockout_until->diffInMinutes(now());
            return response()->json([
                'message' => "Account locked. Try again in $diff minutes."
            ], 423);
        }

        if (Auth::attempt(['email' => $email, 'password' => $credentials['password']])) {
            $request->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = Auth::user();

            $user->update([
                'login_attempts' => 0,
                'lockout_until' => null,
            ]);

            $this->logActivity($user, $request);

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
            ]);
        }

        // Handle failed attempt
        if ($user) {
            $user->increment('login_attempts');
            if ($user->login_attempts >= self::MAX_ATTEMPTS) {
                $user->update(['lockout_until' => now()->addMinutes(self::LOCKOUT_MINUTES)]);
            }
        }

        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    private function logActivity(User $user, Request $request): void
    {
        $agent = $request->header('User-Agent') ?? '';

        // Precise Platform Detection
        $platform = 'Unknown';
        if (preg_match('/Windows NT ([\d\.]+)/', $agent, $matches)) {
            $v = $matches[1];
            $platform = match ($v) {
                '10.0' => 'Windows 10/11',
                '6.3' => 'Windows 8.1',
                '6.2' => 'Windows 8',
                '6.1' => 'Windows 7',
                '6.0' => 'Windows Vista',
                '5.1' => 'Windows XP',
                default => "Windows NT $v"
            };
        } elseif (preg_match('/Mac OS X ([\d_]+)/', $agent, $matches)) {
            $platform = 'macOS ' . str_replace('_', '.', $matches[1]);
        } elseif (preg_match('/Android ([\d\.]+)/', $agent, $matches)) {
            $platform = 'Android ' . $matches[1];
        } elseif (preg_match('/iPhone OS ([\d_]+)/', $agent, $matches)) {
            $platform = 'iOS ' . str_replace('_', '.', $matches[1]);
        } elseif (str_contains($agent, 'Linux')) {
            $platform = 'Linux';
        }

        // Precise Browser Detection (Order Matters!)
        $browser = 'Unknown';
        if (preg_match('/(Edg|Edge)\/([\d\.]+)/', $agent, $matches)) {
            $browser = 'Edge ' . $matches[2];
        } elseif (preg_match('/OPR\/([\d\.]+)/', $agent, $matches)) {
            $browser = 'Opera ' . $matches[1];
        } elseif (preg_match('/Vivaldi\/([\d\.]+)/', $agent, $matches)) {
            $browser = 'Vivaldi ' . $matches[1];
        } elseif (preg_match('/Chrome\/([\d\.]+)/', $agent, $matches)) {
            $browser = 'Chrome ' . (explode('.', $matches[1])[0] ?? $matches[1]);
        } elseif (preg_match('/Firefox\/([\d\.]+)/', $agent, $matches)) {
            $browser = 'Firefox ' . (explode('.', $matches[1])[0] ?? $matches[1]);
        } elseif (preg_match('/Version\/([\d\.]+).*Safari/', $agent, $matches)) {
            $browser = 'Safari ' . $matches[1];
        }

        $device = str_contains($agent, 'Mobi') ? 'Mobile' : 'Desktop';

        LoginActivity::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $agent,
            'device' => $device,
            'platform' => $platform,
            'browser' => $browser,
            'created_at' => now(),
        ]);
    }

    public function getLoginHistory(Request $request)
    {
        return response()->json(
            $request->user()->loginActivities()->orderBy('created_at', 'desc')->limit(20)->get()
        );
    }

    public function getActiveSessions(Request $request)
    {
        $sessions = DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->get(['id', 'ip_address', 'user_agent', 'last_activity']);

        // Format for frontend
        $formatted = $sessions->map(function ($session) use ($request) {
            return [
                'id' => $session->id,
                'is_current' => $session->id === $request->session()->getId(),
                'ip_address' => $session->ip_address,
                'user_agent' => $session->user_agent,
                'last_active' => date('Y-m-d H:i:s', $session->last_activity),
            ];
        });

        return response()->json($formatted);
    }

    public function revokeSession(Request $request, $sessionId)
    {
        DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->where('id', $sessionId)
            ->delete();

        return response()->json(['message' => 'Session revoked']);
    }

    /**
     * Forgot Password
     */
    /**
     * Step 1: Request OTP
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = strtolower(trim($request->email));

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'No account found with this email address.',
                'errors' => ['email' => ['No account found with this email address.']]
            ], 404);
        }

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Debug: Log OTP directly for testing when mail is not set up
        Log::info("PASSWORD RESET OTP for $email: $otp");

        // Store in Cache (email as key) for 15 minutes
        Cache::put('password_reset_otp:' . $email, $otp, now()->addMinutes(15));

        // Send Email
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ResetPasswordOtp($otp));
        } catch (\Exception $e) {
            // Log error but generally return success to user/handle gracefully
            Log::error("Mail Sending Failed: " . $e->getMessage());
            return response()->json(['message' => 'Could not send verification email. Please try again later.'], 500);
        }

        return response()->json([
            'message' => 'Verification code sent to your email address.'
        ]);
    }

    /**
     * Step 2: Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        $email = strtolower(trim($request->email));
        $cachedOtp = Cache::get('password_reset_otp:' . $email);

        if ($cachedOtp && $cachedOtp == $request->otp) {
            return response()->json([
                'message' => 'OTP Verified',
                'valid' => true
            ]);
        }

        return response()->json([
            'message' => 'Invalid or expired code.',
            'valid' => false
        ], 422);
    }

    /**
     * Step 3: Reset Password with OTP
     */
    public function resetPasswordWithOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()->symbols()->mixedCase()],
        ]);

        $email = strtolower(trim($request->email));

        // Re-verify OTP one last time to prevent bypass
        $cachedOtp = Cache::get('password_reset_otp:' . $email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json(['message' => 'Invalid or expired code.'], 422);
        }

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'New password cannot be the same as the old password.',
                'errors' => [
                    'password' => ['Please choose a different password that you haven\'t used before.']
                ]
            ], 422);
        }

        $user->forceFill([
            'password' => Hash::make($request->password)
        ])->setRememberToken(Str::random(60));

        $user->save();

        // Clear OTP
        Cache::forget('password_reset_otp:' . $email);

        return response()->json(['message' => 'Password has been reset successfully.']);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255',
        ]);
        $user->name = $data['name'];
        $this->audit($user, $request);
        $user->save();
        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()->mixedCase()],
        ]);
        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect', 'errors' => ['current_password' => ['Incorrect current password']]], 422);
        }
        if (Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'New password cannot be the same as the old password.', 'errors' => ['password' => ['Choose a new password different from the current one.']]], 422);
        }
        $user->password = Hash::make($data['password']);
        $this->audit($user, $request);
        $user->save();
        return response()->json(['message' => 'Password updated successfully']);
    }

    public function uploadAvatar(Request $request)
    {
        $user = $request->user();
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
        return response()->json(['message' => 'Avatar updated successfully', 'avatar_url' => $user->avatar_url]);
    }

    public function requestEmailChange(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'new_email' => 'required|email|max:255|unique:users,email',
            'current_password' => 'required|string',
        ]);
        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect', 'errors' => ['current_password' => ['Incorrect current password']]], 422);
        }
        $newEmail = strtolower(trim($data['new_email']));
        $domain = substr(strrchr($newEmail, '@'), 1);
        if (!$this->isValidEmailDomain($domain)) {
            return response()->json(['message' => 'Invalid email domain', 'errors' => ['new_email' => ['This email domain does not have valid mail servers']]], 422);
        }
        $otp = rand(100000, 999999);
        Cache::put('email_change_otp:' . $user->id . ':' . $newEmail, $otp, now()->addMinutes(15));
        try {
            Mail::raw("Your verification code is: {$otp}", function ($m) use ($newEmail) {
                $m->to($newEmail)->subject('Verify your new email');
            });
        } catch (\Exception $e) {
            Log::error('Email change OTP send failed: ' . $e->getMessage());
            return response()->json(['message' => 'Could not send verification email. Please try again later.'], 500);
        }
        return response()->json(['message' => 'Verification code sent to your new email address.']);
    }

    public function confirmEmailChange(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'new_email' => 'required|email|max:255|unique:users,email',
            'otp' => 'required|string|size:6',
        ]);
        $newEmail = strtolower(trim($data['new_email']));
        $cacheKey = 'email_change_otp:' . $user->id . ':' . $newEmail;
        $cachedOtp = Cache::get($cacheKey);
        if (!$cachedOtp || $cachedOtp != $data['otp']) {
            return response()->json(['message' => 'Invalid or expired verification code.'], 422);
        }
        if (
            in_array('email_verified_at', $user->getFillable()) ||
            Schema::hasColumn('users', 'email_verified_at')
        ) {
            $user->email_verified_at = null;
        }
        $this->audit($user, $request);
        $user->save();
        Cache::forget($cacheKey);
        return response()->json(['message' => 'Email updated successfully', 'user' => $user]);
    }

    public function deactivateAccount(Request $request)
    {
        $user = $request->user();
        $user->delete();
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Account deactivated. You can contact support to restore within 30 days.']);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
