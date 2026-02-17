<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Log;
use App\Models\LoginActivity;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

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
        Log::info("[AuthController] Registration attempt for email: $email");
        $domain = substr(strrchr($email, "@"), 1);

        // ✅ Full MX validation on submit (with caching)
        if (!$this->isValidEmailDomain($domain)) {
            Log::warning("[AuthController] Registration failed: Invalid email domain for $email");
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

        // Assign role if provided, otherwise default to "Viewer"
        if ($request->role) {
            $user->assignRole(ucfirst(strtolower($request->role)));
        } else {
            $user->assignRole('Viewer');
        }

        Auth::login($user);
        Log::info("[AuthController] User registered and logged in: $email (ID: {$user->id})");

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
        Log::info("[AuthController] Login attempt for email: $email");
        $user = User::where('email', $email)->first();

        if ($user && $user->lockout_until && $user->lockout_until->isFuture()) {
            $diff = $user->lockout_until->diffInMinutes(now());
            Log::warning("[AuthController] Login blocked: Account locked for $email for another $diff mins");
            return response()->json([
                'message' => "Account locked. Try again in $diff minutes."
            ], 423);
        }

        if (Auth::attempt(['email' => $email, 'password' => $credentials['password']])) {
            $request->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = Auth::user();
            Log::info("[AuthController] Login successful for $email (ID: {$user->id})");

            $user->update([
                'login_attempts' => 0,
                'lockout_until' => null,
            ]);

            $this->logActivity($user, $request, 'success');

            $user->tokens()->delete();
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
            ]);
        }

        // Handle failed attempt
        if ($user) {
            $user->increment('login_attempts');
            Log::warning("[AuthController] Login failed for $email. Attempt: {$user->login_attempts}");
            if ($user->login_attempts >= self::MAX_ATTEMPTS) {
                Log::error("[AuthController] Login lockout triggered for $email");
                $user->update(['lockout_until' => now()->addMinutes(self::LOCKOUT_MINUTES)]);
            }
            $this->logActivity($user, $request, 'failed');
        } else {
            Log::warning("[AuthController] Login failed: User not found for email $email");
        }

        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    private function logActivity(User $user, Request $request, string $status = 'success'): void
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
            'status' => $status,
            'created_at' => now(),
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $email = $user ? $user->email : 'unknown';
        Log::info("[AuthController] Logout initiated for user: $email");

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info("[AuthController] Logout successful for $email");

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
