<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;
class AuthRepository
{
    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    public function login(array $credentials)
    {
        try {
            // محاولة إنشاء التوكن باستخدام البريد وكلمة المرور
            if (!$token = JWTAuth::attempt($credentials)) {
                return [
                    'status' => false,
                    'message' => 'بيانات تسجيل الدخول غير صحيحة',
                    'code' => 401
                ];
            }

            $user = auth()->user();

            // تحديث معلومات تسجيل الدخول
            $user->last_login_ip = request()->ip();   // يمكنك إلغاء التعليق بعد إضافة العمود
            $user->last_login_at = now();
            $user->device_token = $user->device_token ?? uniqid('device_', true);
            $user->save();

            // الاستجابة عند النجاح
            return [
                'status' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'code' => 200,
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                    'user' => $user
                ]
            ];

        } catch (JWTException $e) {
            // في حال وجود مشكلة مع إنشاء التوكن
            Log::error('JWT Login Error: '.$e->getMessage());

            return [
                'status' => false,
                'message' => 'تعذر إنشاء التوكن، حاول لاحقًا',
                'code' => 500
            ];
        } catch (\Exception $e) {
            // أي أخطاء غير متوقعة أخرى
            Log::error('Login Error: '.$e->getMessage());

            return [
                'status' => false,
                'message' => 'حدث خطأ غير متوقع، حاول مرة أخرى',
                'code' => 500
            ];
        }
    }

    public function logout(User $user)
    {
        $user->device_token = null;
        $user->save();

        JWTAuth::invalidate(JWTAuth::getToken());
    }
}
