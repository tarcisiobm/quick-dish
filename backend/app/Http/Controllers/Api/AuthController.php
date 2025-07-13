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

        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser?->hasVerifiedEmail()) {
            return response()->json([
                'message' => __('validation.custom.email.unique'),
            ], 409);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        return response()->json([
            'message' => __('auth.user_created')
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'boolean',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember', false);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => __('auth.invalid_credentials')
            ], 401);
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => __('auth.email_not_verified')
            ], 401);
        }

        $user->tokens()->delete();

        $tokenExpiration = $remember ? now()->addDays(7) : null;
        $token = $user->createToken('auth-token', ['*'], $tokenExpiration)->plainTextToken;

        $cookieExpiration = $remember ? 60 * 24 * 7 : 0;

        return response()
            ->json([
                'message' => __('auth.user_authenticated'),
                'user' => $user
            ])
            ->cookie('auth_token', $token, $cookieExpiration, '/', null, true, true, false, 'Strict');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->clearCookie([
            'message' => __('auth.logged_out')
        ]);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return $this->clearCookie([
            'message' => __('auth.logged_out_all')
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
            'message' => __('auth.user_recovered'),
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
                'message' => __('auth.current_password_incorrect'),
            ], 403);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json([
            'message' => __('auth.password_changed')
        ]);
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

        return response()->json([
            'message' => __('auth.email_updated')
        ]);
    }

    public function recoverPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json([
                'message' => __('auth.password_reset_sent')
            ])
            : response()->json([
                'message' => __($status)
            ], 422);
    }

    public function validateToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
        ]);

        if (!Password::getRepository()->exists($request->email, $request->token)) {
            return response()->json([
                'message' => __('auth.invalid_token'),
            ], 422);
        }

        return response()->json([
            'message' => __('auth.valid_token')
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
            ? response()->json([
                'message' => __('auth.password_reset_success')
            ])
            : response()->json([
                'message' => __($status)
            ], 422);
    }

    private function clearCookie(array $data): JsonResponse
    {
        return response()
            ->json($data)
            ->cookie('auth_token');
    }
}
