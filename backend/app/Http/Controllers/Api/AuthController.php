<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'email' => 'required|string|max:150|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'error' => $validator->errors(),
                'i18n' => 'api.validationError'
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user && $user->email_verified_at) {
            return response()->json([
                'status' => 'error',
                'message' => 'The email has already been taken.',
                'i18n' => 'api.theEmailHasAlreadyBeenTaken'
            ], 409);
        }

        $user = User::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]
        );

        event(new Registered($user));
        return response()->json([
            'message' => 'User created successfully.',
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'error' => $validator->errors(),
                'i18n' => 'api.validationError'
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid login credentials."',
                'i18n' => 'api.invalidLoginCredentials'
            ], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email not verified',
                'i18n' => 'api.emailNotVerified'
            ], 403);
        }

        $user->currentAccessToken()?->delete();
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
        $request->user()->currentAccessToken()?->delete();
        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logged out from all devices successfully.'
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
            'message' => 'User recovered successfully.'
        ]);
    }
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'error' => $validator->errors(),
                'i18n' => 'api.validationError'
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Current password is incorrect.',
                'i18n' => 'api.currentPasswordIncorrect'
            ], 403);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully.'
        ]);
    }

    public function changeEmail($request)
    {
        $validator = Validator::make($request->all(), [
            'new_email' => 'required|string|max:150|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'error' => $validator->errors(),
                'i18n' => 'api.validationError'
            ], 422);
        }
        $user = $request->user();
        $user->email = $request->new_email;
        $user->email_verified_at = null;
        $user->save();
        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Email updated successfully. Verification email sent.'
        ]);
    }

    public function recoverPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'error' => $validator->errors(),
                'i18n' => 'api.validationError'
            ], 422);
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status != Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => 'error',
                'message' => $status
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => $status
        ]);
    }

    public function validateToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'error' => $validator->errors(),
                'i18n' => 'api.validationError'
            ], 422);
        }

        $isFromUser = $this->compareToken($request->token, $request->email);

        if (!$isFromUser) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid token',
                'i18n' => 'api.invalidToken'
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Valid token',
            'i18n' => 'api.validToken'
        ]);
    }

    private function compareToken(string $token, string $email): bool
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        return $record && Hash::check($token, $record->token);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'error' => $validator->errors(),
                'i18n' => 'api.validationError'
            ], 422);
        }

        $status = Password::reset($request->all(), function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();

            event(new PasswordReset($user));
        });

        if ($status != Password::PASSWORD_RESET) {
            return response()->json([
                'status' => 'error',
                'message' => $status
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => $status
        ]);
    }
}
