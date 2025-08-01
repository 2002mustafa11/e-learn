<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(array $data)
    {
        try {
            $data['password'] = Hash::make($data['password']);

            $user = $this->authRepository->register($data);

            $token = JWTAuth::fromUser($user);

            return [
                'success' => true,
                'message' => 'Account registered successfully',
                'code' => 201,
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                    'user' => $user
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Register Error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Registration failed, please try again',
                'code' => 500,
                'data' => []
            ];
        }
    }

    public function login(array $credentials): array
    {
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return [
                    'success' => false,
                    'message' => 'Invalid login credentials',
                    'code' => 401,
                    'data' => []
                ];
            }

            $user = auth()->user();
            $deviceToken = $user->device_token ?? uniqid('device_', true);

            $this->authRepository->Login($user, request()->ip(), $deviceToken);

            return [
                'success' => true,
                'message' => 'Login successful',
                'code' => 200,
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                    'user' => $user
                ]
            ];

        } catch (JWTException $e) {
            Log::error('JWT Login Error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Could not create token, please try again later',
                'code' => 500,
                'data' => []
            ];
        } catch (\Exception $e) {
            Log::error('Login Error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Unexpected error occurred',
                'code' => 500,
                'data' => []
            ];
        }
    }

    public function logout()
    {
        $this->authRepository->logout(auth()->user());
    }
}
