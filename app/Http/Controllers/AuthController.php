<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // User Registration
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),  // Bcrypt hashing
            'email_verified_at' => now(),
        ]);

        // Optional: Create Sanctum token for immediate login after register
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token ?? null,  // Remove if you don't want auto-login
        ], 201);
    }

    // Placeholder for login (implement next)
    public function login(Request $request)
    {
        // TODO: Implement login logic
        return response()->json(['message' => 'Login endpoint']);
    }

    // Placeholder for forgot password (email reset link)
    public function forgotPassword(Request $request)
    {
        // TODO: Use Password::broker()->sendResetLink()
        return response()->json(['message' => 'Forgot password endpoint']);
    }

    // Placeholder for reset password
    public function resetPassword(Request $request)
    {
        // TODO: Implement
    }
}