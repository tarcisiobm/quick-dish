<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthService
{
    public function getRedirectUrl(string $provider): array
    {
        try {
            $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();

            return [
                'success' => true,
                'redirect_url' => $url,
                'status' => 200,
            ];
        } catch (Exception) {
            return [
                'success' => false,
                'error' => 'Error generating redirect link.',
                'status' => 500,
            ];
        }
    }

    public function handleCallback(string $provider): array
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $email = $socialUser->getEmail();

            $existingUser = User::where('email', $email)->first();

            if ($existingUser?->provider && $existingUser->provider !== $provider) {
                return [
                    'success' => false,
                    'error' => 'User already has a different provider.',
                    'status' => 409,
                ];
            }

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                    'password' => $existingUser?->password ?? Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                    'provider_name' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ]
            );

            if (!$user->status) {
                return [
                    'success' => false,
                    'error' => __('auth.inactive'),
                    'status' => 403,
                ];
            }

            $user->tokens()->delete();
            $token = $user->createToken('auth-token', ['*'], now()->addDays(7))->plainTextToken;

            return [
                'success' => true,
                'token' => $token,
                'user' => $user,
                'status' => 200,
            ];
        } catch (Exception) {
            return [
                'success' => false,
                'error' => 'Authentication failed. Please try again.',
                'status' => 500,
            ];
        }
    }
}
