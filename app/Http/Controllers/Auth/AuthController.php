<?php

namespace App\Http\Controllers\Auth;

use App\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
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
        $user = $this->authService->register($request->validated());
        return $this->successResponse([
            'user' => $user
        ],'Account registered successfully');
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->only('email', 'password'));
        if (!$result) {
            return $this->errorResponse('Invalid login credentials', [], 401);
        }
        return $this->successResponse($result, 'Login successful', 200);
    }


    public function logout()
    {
        $this->authService->logout();
        return $this->successResponse([], 'Logout successful');
    }
}
