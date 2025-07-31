<?php

namespace App\Http\Controllers\Auth;

use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:student,parent,teacher,admin',
        ]);

        $user = $this->authService->register($validated);

        return response()->json([
            'message' => 'تم تسجيل الحساب بنجاح',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        dd($request->input());
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        $result = $this->authService->login($validated);

        if (!$result) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات تسجيل الدخول غير صحيحة',
            ], 401);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => $result,
        ], 200);
    }


    public function logout()
    {
        $this->authService->logout();

        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }
}
