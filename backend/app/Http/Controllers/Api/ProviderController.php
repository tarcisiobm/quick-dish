<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProviderController extends Controller
{
    public function redirectToProvider(string $provider): JsonResponse
    {
        try {
            $redirectUrl = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
            return response()->json([
                'redirect_url' => $redirectUrl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error generating redirect link.',
                'i18n' => 'errorGeneratingRedirectLink',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function handleProviderCallback(string $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user?->provider) {
                return view('social-callback', [
                    'status' => 'error',
                    'error' => 'User already have a provider.'
                ]);
            }

            $updatedUser = $this->updateOrCreateFromSocialite($user, $provider, $socialUser);
            $updatedUser->currentAccessToken()?->delete();

            $token = $updatedUser->createToken('auth-token', ['*'], now()->addDays(7))->plainTextToken;
            return view('social-callback', [
                'status' => 'success',
                'token' => $token,
                'user' => $updatedUser->toJson()
            ]);
        } catch (\Exception $e) {
            return view('social-callback', [
                'status' => 'error',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function updateOrCreateFromSocialite(User|null $user, string $provider, $socialUser)
    {
        return User::updateOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'email_verified_at' => now(),
                'password' => $user?->password ?? Hash::make(Str::random(24)),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
            ]
        );
    }
}
