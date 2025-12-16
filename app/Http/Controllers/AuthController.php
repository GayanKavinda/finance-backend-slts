<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    // User Registration
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
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
