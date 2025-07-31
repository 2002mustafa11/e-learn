<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(array $data)
    {
        return $this->authRepository->register($data);
    }

    public function login(array $credentials)
    {
        $token = $this->authRepository->login($credentials);

        if (!$token) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ];
    }

    public function logout()
    {
        $this->authRepository->logout(auth()->user());
    }
}
