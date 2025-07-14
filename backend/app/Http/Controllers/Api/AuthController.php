<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|string|max:150|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $result = $this->authService->register($request->all());

        return response()->json([
            'message' => $result['message']
        ], $result['status']);
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

        $result = $this->authService->login($credentials, $remember);

        if (!$result['success']) {
            return response()->json([
                'message' => $result['message']
            ], $result['status']);
        }

        $cookieExpiration = $result['remember'] ? 60 * 24 * 7 : 0;

        return response()
            ->json([
                'message' => $result['message'],
                'user' => $result['user']
            ])
            ->cookie('auth_token', $result['token'], $cookieExpiration, '/', null, true, true, false, 'Strict');
    }

    public function logout(Request $request): JsonResponse
    {
        $result = $this->authService->logout($request->user());

        return $this->clearCookie([
            'message' => $result['message']
        ]);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $result = $this->authService->logoutAll($request->user());

        return $this->clearCookie([
            'message' => $result['message']
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

        $result = $this->authService->changePassword(
            $request->user(),
            $request->current_password,
            $request->new_password
        );

        return response()->json([
            'message' => $result['message']
        ], $result['status']);
    }

    public function changeEmail(Request $request): JsonResponse
    {
        $request->validate([
            'new_email' => 'required|string|max:150|email|unique:users,email',
        ]);

        $result = $this->authService->changeEmail(
            $request->user(),
            $request->new_email
        );

        return response()->json([
            'message' => $result['message']
        ], $result['status']);
    }

    public function recoverPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $result = $this->authService->sendPasswordResetLink($request->email);

        return response()->json([
            'message' => $result['message']
        ], $result['status']);
    }

    public function validateToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
        ]);

        $result = $this->authService->validateResetToken(
            $request->email,
            $request->token
        );

        return response()->json([
            'message' => $result['message']
        ], $result['status']);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $result = $this->authService->resetPassword($request->all());

        return response()->json([
            'message' => $result['message']
        ], $result['status']);
    }

    private function clearCookie(array $data): JsonResponse
    {
        return response()
            ->json($data)
            ->cookie('auth_token');
    }
}
