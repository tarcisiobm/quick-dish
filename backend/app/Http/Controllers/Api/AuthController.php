<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    private const TOKEN_EXPIRY_DAYS = 7;
    private const COOKIE_EXPIRY_MINUTES = 60 * 24 * 7;

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|string|max:150|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser?->hasVerifiedEmail()) {
            return $this->errorResponse('The email has already been taken.', 'api.theEmailHasAlreadyBeenTaken', 409);
        }

        $user = User::updateOrCreate(
            ['email' => $request->email],
            $request->only(['name', 'phone']) + ['password' => Hash::make($request->password)]
        );

        event(new Registered($user));

        return response()->json(['message' => 'User created successfully.'], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return $this->errorResponse('Invalid login credentials.', 'api.invalidLoginCredentials', 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return $this->errorResponse('Email not verified', 'api.emailNotVerified', 403);
        }

        return $this->authenticateUser($user);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();
        return $this->clearAuthCookie(['message' => 'Logged out successfully.']);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return $this->clearAuthCookie(['message' => 'Logged out from all devices successfully.']);
    }

    public function me(Request $request): JsonResponse
    {
        if (!$request->user()) {
            return $this->errorResponse('Unauthenticated', null, 401);
        }

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
            return $this->errorResponse('Current password is incorrect.', 'api.currentPasswordIncorrect', 403);
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

        if ($status !== Password::RESET_LINK_SENT) {
            return $this->errorResponse($status, null, 422);
        }

        return response()->json(['status' => 'success', 'message' => $status]);
    }

    public function validateToken(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
        ]);

        if (!$this->isValidPasswordResetToken($data['token'], $data['email'])) {
            return $this->errorResponse('Invalid token', 'api.invalidToken', 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Valid token',
            'i18n' => 'api.validToken',
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

        if ($status !== Password::PASSWORD_RESET) {
            return $this->errorResponse($status, null, 422);
        }

        return response()->json(['status' => 'success', 'message' => $status]);
    }

    private function isValidPasswordResetToken(string $token, string $email): bool
    {
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();
        return $record && Hash::check($token, $record->token);
    }

    private function authenticateUser(User $user): JsonResponse
    {
        $user->currentAccessToken()?->delete();
        $token = $user->createToken('auth-token', ['*'], now()->addDays(self::TOKEN_EXPIRY_DAYS))->plainTextToken;

        return response()
            ->json(['message' => 'User authenticated successfully.', 'user' => $user])
            ->cookie('auth_token', $token, self::COOKIE_EXPIRY_MINUTES, '/', null, true, true, false, 'Strict');
    }

    private function clearAuthCookie(array $data): JsonResponse
    {
        return response()
            ->json($data)
            ->cookie('auth_token', '', -1, '/', null, true, true, false, 'Strict');
    }

    private function errorResponse(string $message, ?string $i18n = null, int $status = 422): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($i18n) {
            $response['i18n'] = $i18n;
        }

        return response()->json($response, $status);
    }
}
