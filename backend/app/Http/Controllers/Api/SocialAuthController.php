<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SocialAuthService;
use Illuminate\Http\JsonResponse;

class SocialAuthController extends Controller
{
    public function __construct(
        private readonly SocialAuthService $socialAuthService
    ) {
    }

    public function getAuthUrl(string $provider): JsonResponse
    {
        $result = $this->socialAuthService->getRedirectUrl($provider);

        if (!$result['success']) {
            return response()->json([
                'error' => $result['error']
            ], $result['status']);
        }

        return response()->json([
            'redirect_url' => $result['redirect_url']
        ]);
    }

    public function handleCallback(string $provider)
    {
        $result = $this->socialAuthService->handleCallback($provider);

        if (!$result['success']) {
            return $this->errorView($result['error']);
        }

        return view('social-callback', [
            'status' => 'success',
            'token' => $result['token'],
            'user' => $result['user']->toJson(),
        ]);
    }

    private function errorView(string $error)
    {
        return view('social-callback', [
            'status' => 'error',
            'error' => $error
        ]);
    }
}
