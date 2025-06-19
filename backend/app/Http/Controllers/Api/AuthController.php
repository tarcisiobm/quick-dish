<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'email' => 'required|string|max:150|email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) return response()->json($validator->errors(), 422);

        User::where('email', $request->email)
            ->whereNull('email_verified_at')
            ->delete();

        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'The email has already been taken.'
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        return response()->json([
            'message' => 'User created successfully. Please check your email to verify your account.',
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Your email address is not verified. Please check your inbox.'], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth-token', ['*'], now()->addDays(7))->plainTextToken;

        return response()->json([
            'message' => 'User authenticated successfully.',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $request->user()]);
    }
}
