<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Traits\HandleServiceErrors;
use Throwable;
use app\Models\User;
class AuthService
{
    use HandleServiceErrors;
    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(array $data): array
    {
        try {
            $data['password'] = Hash::make($data['password']);
            $user = $this->authRepository->register($data);

            $token = JWTAuth::fromUser($user);

            return [
                'success' => true,
                'message' => 'Account registered successfully',
                'code'    => 201,
                'data'    => [
                    'token' => $token,
                    'user'  => $user
                ]
            ];
        } catch (Throwable $e) {
            return $this->handleServiceException($e, 'Register Error');
        }
    }

    public function login(array $credentials): array
    {
        if (!$token = auth()->attempt($credentials)) {
            return [
                'success' => false,
                'message' => 'Invalid login credentials'
            ];
        }

        $user = auth()->user();

        if ($user->last_login_at !== null) {
            auth()->logout();
            return [
                'success' => false,
                'message' => 'You are already logged in on another device.'
            ];
        }
        $this->authRepository->login($user);
        return [
            'success'      => true,
            'message'      => 'Login successful',
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60,
            'user'         => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ]
        ];
    }

    public function logout()
    {
        $this->authRepository->logout(auth()->user());
    }
}
