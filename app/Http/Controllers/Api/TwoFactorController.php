<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\TwoFactorCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    const OTP_TTL_MINUTES = 10;
    const MAX_VERIFY_ATTEMPTS = 5;

    /**
     * Enable 2FA for the authenticated user (requires current password).
     */
    public function enable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Incorrect password. Two-factor authentication was not enabled.',
                'errors' => ['password' => ['The provided password is incorrect.']],
            ], 422);
        }

        $user->forceFill(['two_factor_enabled' => true])->save();

        Log::info("[TwoFactor] 2FA enabled for {$user->email} (ID: {$user->id})");

        return response()->json([
            'message' => 'Two-factor authentication enabled. You will be asked for a verification code at your next login.',
            'two_factor_enabled' => true,
        ]);
    }

    /**
     * Disable 2FA for the authenticated user (requires current password).
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Incorrect password. Two-factor authentication was not disabled.',
                'errors' => ['password' => ['The provided password is incorrect.']],
            ], 422);
        }

        $user->forceFill(['two_factor_enabled' => false])->save();

        Log::info("[TwoFactor] 2FA disabled for {$user->email} (ID: {$user->id})");

        return response()->json([
            'message' => 'Two-factor authentication disabled.',
            'two_factor_enabled' => false,
        ]);
    }

    /**
     * Current 2FA state for the authenticated user.
     */
    public function status(Request $request)
    {
        return response()->json([
            'two_factor_enabled' => (bool) $request->user()->two_factor_enabled,
        ]);
    }

    /**
     * Complete a login that was challenged by 2FA.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'twofa_token' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        $payload = Cache::get('twofa:' . $request->twofa_token);

        if (!$payload || ($payload['expires_at'] ?? 0) < now()->timestamp) {
            return response()->json(['message' => 'Verification session expired. Please sign in again.'], 422);
        }

        if (($payload['attempts'] ?? 0) >= self::MAX_VERIFY_ATTEMPTS) {
            Cache::forget('twofa:' . $request->twofa_token);
            return response()->json(['message' => 'Too many incorrect codes. Please sign in again.'], 429);
        }

        if (!hash_equals((string) $payload['otp'], (string) $request->otp)) {
            $payload['attempts'] = ($payload['attempts'] ?? 0) + 1;
            // Preserve the original challenge expiry while counting attempts.
            Cache::put(
                'twofa:' . $request->twofa_token,
                $payload,
                now()->setTimestamp($payload['expires_at'])
            );
            return response()->json(['message' => 'Invalid verification code.'], 422);
        }

        Cache::forget('twofa:' . $request->twofa_token);

        $user = User::find($payload['user_id']);
        if (!$user) {
            return response()->json(['message' => 'Account no longer exists.'], 422);
        }

        \Auth::login($user);
        $request->session()->regenerate();

        $user->update(['login_attempts' => 0, 'lockout_until' => null]);

        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        Log::info("[TwoFactor] 2FA verification successful for {$user->email} (ID: {$user->id})");

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Resend the verification code for a pending 2FA challenge.
     */
    public function resend(Request $request)
    {
        $request->validate([
            'twofa_token' => 'required|string',
        ]);

        $payload = Cache::get('twofa:' . $request->twofa_token);

        if (!$payload) {
            return response()->json(['message' => 'Verification session expired. Please sign in again.'], 422);
        }

        $user = User::find($payload['user_id']);
        if (!$user) {
            return response()->json(['message' => 'Account no longer exists.'], 422);
        }

        self::refreshChallenge($request->twofa_token, $user);

        return response()->json(['message' => 'A new verification code has been sent to your email.']);
    }

    /**
     * Start a fresh 2FA challenge for a user: generates the one-time code,
     * caches it against a challenge token, and emails the code.
     * Returns the challenge token (never the OTP) to the caller.
     */
    public static function startChallenge(User $user): string
    {
        $token = Str::random(48);
        $otp = rand(100000, 999999);

        Cache::put('twofa:' . $token, [
            'user_id' => $user->id,
            'otp' => $otp,
            'attempts' => 0,
            'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES)->timestamp,
        ], now()->addMinutes(self::OTP_TTL_MINUTES));

        try {
            Mail::to($user->email)->send(new TwoFactorCode($otp));
        } catch (\Exception $e) {
            Log::error("[TwoFactor] Failed to send 2FA code to {$user->email}: " . $e->getMessage());
        }

        Log::info("[TwoFactor] 2FA challenge issued for {$user->email} (ID: {$user->id})");

        return $token;
    }

    /**
     * Replace the code for an existing challenge (resend flow).
     */
    public static function refreshChallenge(string $challengeToken, User $user): void
    {
        $otp = rand(100000, 999999);

        Cache::put('twofa:' . $challengeToken, [
            'user_id' => $user->id,
            'otp' => $otp,
            'attempts' => 0,
            'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES)->timestamp,
        ], now()->addMinutes(self::OTP_TTL_MINUTES));

        try {
            Mail::to($user->email)->send(new TwoFactorCode($otp));
        } catch (\Exception $e) {
            Log::error("[TwoFactor] Failed to resend 2FA code to {$user->email}: " . $e->getMessage());
        }
    }
}
