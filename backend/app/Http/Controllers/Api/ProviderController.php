<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class ProviderController extends Controller
{
    public function redirectToProvider(string $provider): JsonResponse
    {
        $validProviders = ['google', 'facebook', 'apple'];
        if (!in_array($provider, $validProviders)) {
            return response()->json(['message' => 'Invalid provider'], 400);
        }

        try {
            $redirectUrl = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
            return response()->json([
                'redirect_url' => $redirectUrl
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error generating redirect URL', 'error' => $e->getMessage()], 500);
        }
    }

    public function handleProviderCallback(string $provider)
    {
        $validProviders = ['google', 'facebook', 'apple'];
        if (!in_array($provider, $validProviders)) {
            return view('auth.social-callback', ['error' => 'Invalid provider.']);
        }

        try {
            Log::info('try');
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $user = User::where('email', $socialUser->getEmail())->first();

            $user
                ? $this->updateUserProviderInfo($socialUser, $user, $provider)
                : $user = $this->createUserFromSocialite($socialUser, $provider);

            $user->tokens()->delete();
            $token = $user->createToken('auth-token', ['*'], now()->addDays(7))->plainTextToken;
            return view('social-callback', [
                'status' => 'success',
                'token' => $token,
                'user' => $user->toJson()
            ]);
        } catch (\Exception $e) {
            Log::info('catch');
            Log::info($e->getMessage());
            return view('social-callback', [
                'status' => 'error',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function createUserFromSocialite($socialUser, string $provider): User
    {
        return User::create([
            'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
            'email' => $socialUser->getEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make(Str::random(24)),
            'provider_name' => $provider,
            'provider_id' => $socialUser->getId(),
            'avatar' => $socialUser->getAvatar(),
        ]);
    }

    private function updateUserProviderInfo($socialUser, User $user, string $provider,): void
    {
        $updateData = [];
        if (!$user->provider) {
            $updateData['provider'] = $provider;
            $updateData['provider_id'] = $socialUser->getId();
        }

        if (!$user->avatar && $socialUser->getAvatar()) {
            $updateData['avatar'] = $socialUser->getAvatar();
        }

        if (!$user->email_verified_at) {
            $updateData['email_verified_at'] = now();
        }

        if (!empty($updateData)) {
            $user->update($updateData);
        }
    }
}
