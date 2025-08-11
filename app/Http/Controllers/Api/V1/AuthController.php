<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Services\AuthService;
use App\Services\UserActivityService;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly UserActivityService $activityService
    ) {}

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register($request->validated());
        return ApiResponse::success($result, 'User registered successfully');
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        if (!$result) {
            return ApiResponse::error('Invalid credentials', 401);
        }

        return ApiResponse::success($result, 'User logged in successfully');
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return ApiResponse::success([], 'Logged out successfully');
    }

    public function me()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return ApiResponse::success($user, 'Authenticated user');
    }
}
