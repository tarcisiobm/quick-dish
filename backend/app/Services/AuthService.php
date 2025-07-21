<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthService
{
    public function register(array $data): array
    {
        $existingUser = User::where('email', $data['email'])->first();

        if ($existingUser?->hasVerifiedEmail()) {
            return [
                'success' => false,
                'message' => __('validation.custom.email.unique'),
                'status' => 409
            ];
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        event(new Registered($user));

        return [
            'success' => true,
            'message' => __('auth.user_created'),
            'status' => 201
        ];
    }

    public function login(array $credentials, bool $remember = false): array
    {
        if (!Auth::attempt($credentials)) {
            return [
                'success' => false,
                'message' => __('auth.invalid_credentials'),
                'status' => 401
            ];
        }

        $user = Auth::user();

        if (!$user->status) {
            Auth::logout();
            return [
                'success' => false,
                'message' => __('auth.inactive'),
                'status' => 403
            ];
        }

        if (!$user->hasVerifiedEmail()) {
            return [
                'success' => false,
                'message' => __('auth.email_not_verified'),
                'status' => 401
            ];
        }

        $user->tokens()->delete();

        $tokenExpiration = match ($remember) {
            true => now()->addDays(7),
            false => null,
        };

        $token = $user->createToken('auth-token', ['*'], $tokenExpiration)->plainTextToken;

        return [
            'success' => true,
            'message' => __('auth.user_authenticated'),
            'user' => $user,
            'token' => $token,
            'remember' => $remember,
            'status' => 200
        ];
    }

    public function logout(User $user): array
    {
        $user->currentAccessToken()->delete();

        return [
            'success' => true,
            'message' => __('auth.logged_out'),
            'status' => 200
        ];
    }

    public function logoutAll(User $user): array
    {
        $user->tokens()->delete();

        return [
            'success' => true,
            'message' => __('auth.logged_out_all'),
            'status' => 200
        ];
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword): array
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return [
                'success' => false,
                'message' => __('auth.current_password_incorrect'),
                'status' => 403
            ];
        }

        $user->update(['password' => Hash::make($newPassword)]);

        return [
            'success' => true,
            'message' => __('auth.password_changed'),
            'status' => 200
        ];
    }

    public function changeEmail(User $user, string $newEmail): array
    {
        $user->update([
            'email' => $newEmail,
            'email_verified_at' => null,
        ]);

        $user->sendEmailVerificationNotification();

        return [
            'success' => true,
            'message' => __('auth.email_updated'),
            'status' => 200
        ];
    }

    public function sendPasswordResetLink(string $email): array
    {
        $resetLinkStatus = Password::sendResetLink(['email' => $email]);

        $success = $resetLinkStatus === Password::RESET_LINK_SENT;
        $message = $success ? __('auth.password_reset_sent') : __('auth.error_sending_password_reset');
        $status = $success ? 200 : 422;

        return [
            'success' => $success,
            'message' => $message,
            'status' => $status
        ];
    }

    public function validateResetToken(string $email, string $token): array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Password::getRepository()->exists($user, $token)) {
            return [
                'success' => false,
                'message' => __('auth.invalid_token'),
                'status' => 422
            ];
        }

        return [
            'success' => true,
            'message' => __('auth.valid_token'),
            'status' => 200
        ];
    }

    public function resetPassword(array $data): array
    {
        $resetPasswordStatus = Password::reset($data, function ($user, $password) {
            $user->update(['password' => Hash::make($password)]);
            event(new PasswordReset($user));
        });

        $success = $resetPasswordStatus === Password::PASSWORD_RESET;
        $message = $success ? __('auth.password_reset_success') : __('auth.error_reseting_password');
        $status = $success ? 200 : 422;

        return [
            'success' => $success,
            'message' => $message,
            'status' => $status
        ];
    }
}
