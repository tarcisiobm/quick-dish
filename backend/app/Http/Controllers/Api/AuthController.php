<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|string|max:150|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        return response()->json(['message' => 'User created successfully.'], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                "status" => "error",
                "message" => "Invalid login credentials",
                "i18n" => "api.invalidCredentials"
            ], 401);
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                "status" => "error",
                "message" => "Email not verified",
                "i18n" => "api.emailNotVerified"
            ], 401);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth-token', ['*'], now()->addDays(7))->plainTextToken;

        return response()
            ->json(['message' => 'User authenticated successfully.', 'user' => $user])
            ->cookie('auth_token', $token, 10080, '/', null, true, true, false, 'Strict');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->clearCookie(['message' => 'Logged out successfully.']);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return $this->clearCookie(['message' => 'Logged out from all devices successfully.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
            'message' => 'User recovered successfully.',
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                "status" => "error",
                "message" => "Current password is incorrect.",
                "i18n" => "api.currentPasswordIncorrect"
            ], 403);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Password changed successfully.']);
    }

    public function changeEmail(Request $request): JsonResponse
    {
        $request->validate([
            'new_email' => 'required|string|max:150|email|unique:users,email',
        ]);

        $user = $request->user();
        $user->update([
            'email' => $request->new_email,
            'email_verified_at' => null,
        ]);

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Email updated successfully. Verification email sent.']);
    }

    public function recoverPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => 'success', 'message' => $status])
            : $this->error($status, 422);
    }

    public function validateToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
        ]);

        if (!Password::getRepository()->exists($request->email, $request->token)) {
            return response()->json([
                "status" => "error",
                "message" => "Invalid token.",
                "i18n" => "api.invalidToken",
            ], 422);
        }

        return response()->json([
            "status" => "success",
            "message" => "Valid token",
            "i18n" => "api.validToken"
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset($request->all(), function ($user, $password) {
            $user->update(['password' => Hash::make($password)]);
            event(new PasswordReset($user));
        });

        return $status === Password::PASSWORD_RESET
            ? response()->json(['status' => 'success', 'message' => $status])
            : $this->error($status, 422);
    }

    private function clearCookie(array $data): JsonResponse
    {
        return response()
            ->json($data)
            ->cookie('auth_token');
    }

    private function error(string $message, int $status = 422): JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => $message], $status);
    }
}
