<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordResetController extends Controller
{
    /**
     * Step 1: Request OTP
     */
    public function forgotPassword(Request $request)
    {
        Log::info("[PasswordResetController] Password reset requested", ['email' => $request->email]);
        $request->validate(['email' => 'required|email']);
        $email = strtolower(trim($request->email));

        $user = User::where('email', $email)->first();

        if (!$user) {
            Log::warning("[PasswordResetController] Password reset failed: No user found for $email");
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
            Mail::to($user->email)->send(new \App\Mail\ResetPasswordOtp($otp));
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
        Log::info("[PasswordResetController] Verifying OTP", ['email' => $request->email]);
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        $email = strtolower(trim($request->email));
        $cachedOtp = Cache::get('password_reset_otp:' . $email);

        if ($cachedOtp && $cachedOtp == $request->otp) {
            Log::info("[PasswordResetController] OTP verification successful for $email");
            return response()->json([
                'message' => 'OTP Verified',
                'valid' => true
            ]);
        }

        Log::warning("[PasswordResetController] OTP verification failed for $email");
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
        Log::info("[PasswordResetController] Attempting password reset with OTP", ['email' => $request->email]);
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()->symbols()->mixedCase()],
        ]);

        $email = strtolower(trim($request->email));

        // Re-verify OTP one last time to prevent bypass
        $cachedOtp = Cache::get('password_reset_otp:' . $email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            Log::warning("[PasswordResetController] Password reset failed: Invalid or expired OTP for $email");
            return response()->json(['message' => 'Invalid or expired code.'], 422);
        }

        $user = User::where('email', $email)->first();

        if (Hash::check($request->password, $user->password)) {
            Log::warning("[PasswordResetController] Password reset failed: New password same as old for $email");
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
        Log::info("[PasswordResetController] Password reset successful for $email");

        // Clear OTP
        Cache::forget('password_reset_otp:' . $email);

        return response()->json(['message' => 'Password has been reset successfully.']);
    }
}
