<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class ProviderController extends Controller
{
    private const TOKEN_EXPIRY_DAYS = 7;
    private const RANDOM_PASSWORD_LENGTH = 24;

    public function redirectToProvider(string $provider): JsonResponse
    {
        try {
            $redirectUrl = Socialite::driver($provider)
                ->stateless()
                ->redirect()
                ->getTargetUrl();

            return response()->json(['redirect_url' => $redirectUrl]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error generating redirect link.',
                'i18n' => 'api.errorGeneratingRedirectLink',
            ], 500);
        }
    }

    public function handleProviderCallback(string $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $existingUser = User::where('email', $socialUser->getEmail())->first();

            if ($this->hasConflictingProvider($existingUser, $provider)) {
                return $this->errorResponse('User already has a different provider.');
            }

            $user = $this->createOrUpdateUser($existingUser, $provider, $socialUser);
            $token = $this->generateAuthToken($user);

            return $this->successResponse($user, $token);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    private function hasConflictingProvider(?User $user, string $provider): bool
    {
        return $user?->provider && $user->provider !== $provider;
    }

    private function createOrUpdateUser(?User $existingUser, string $provider, SocialiteUser $socialUser): User
    {
        return User::updateOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'email_verified_at' => now(),
                'password' => $existingUser?->password ?? Hash::make(Str::random(self::RANDOM_PASSWORD_LENGTH)),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
            ]
        );
    }

    private function generateAuthToken(User $user): string
    {
        $user->currentAccessToken()?->delete();

        return $user->createToken(
            'auth-token',
            ['*'],
            now()->addDays(self::TOKEN_EXPIRY_DAYS)
        )->plainTextToken;
    }

    private function successResponse(User $user, string $token)
    {
        return view('social-callback', [
            'status' => 'success',
            'token' => $token,
            'user' => $user->toJson(),
        ]);
    }

    private function errorResponse(string $error)
    {
        return view('social-callback', [
            'status' => 'error',
            'error' => $error,
        ]);
    }
}
