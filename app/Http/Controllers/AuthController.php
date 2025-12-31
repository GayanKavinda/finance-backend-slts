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

class AuthController extends Controller
{
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

        // Normalize email
        $credentials['email'] = strtolower(trim($credentials['email']));

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json([
                'message' => 'Login successful',
                'user' => Auth::user(),
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
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

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $user->forceFill([
            'password' => Hash::make($request->password)
        ])->setRememberToken(Str::random(60));

        $user->save();

        // Clear OTP
        Cache::forget('password_reset_otp:' . $email);

        return response()->json(['message' => 'Password has been reset successfully.']);
    }

    /**
     * Update User Profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => strtolower(trim($request->email)),
        ]);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Update User Password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()->symbols()->mixedCase()],
        ]);

        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
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
