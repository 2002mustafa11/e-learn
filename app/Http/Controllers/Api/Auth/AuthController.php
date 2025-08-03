<?php

namespace App\Http\Controllers\Api\Auth;

use App\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Exception;

class AuthController extends Controller
{
    use ApiResponse;
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        // dd($request->validated());
        try {
            $result = $this->authService->register($request->validated());

            if (!$result['success']) {
                return $this->errorResponse($result['message'], [], $result['code']);
            }

            return $this->successResponse($result['data'], $result['message'], $result['code']);
        } catch (Exception $e) {
            return $this->errorResponse('An error occurred during registration', [], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $result = $this->authService->login($request->only('email', 'password'));
            if (isset($result['success']) && !$result['success']) {
                return $this->errorResponse($result['message'], [], 403);
            }
            return $this->successResponse($result, 'Login successful', 200);
        } catch (Exception $e) {
            return $this->errorResponse('An error occurred during login', [], 500);
        }
    }

    public function logout()
    {
        try {
            $this->authService->logout();
            return $this->successResponse([], 'Logout successful');
        } catch (Exception $e) {
            return $this->errorResponse('An error occurred during logout', [], 500);
        }
    }
}