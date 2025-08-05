<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\YouTub\YouTubeAuthController;
use App\Http\Controllers\Api\YouTub\YouTubeUploadController;
use App\Http\Controllers\Api\Auth\AuthController;
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout']);
});
//
Route::middleware('auth:api')->prefix('user')->group(function () {
Route::get('/', [UserController::class, 'index']);
Route::post('students/{parentId}/assign', [UserController::class, 'assignParent']);
Route::get('/{id}', [UserController::class, 'show']);
Route::put('/{id}', [UserController::class, 'update']);
Route::delete('/{id}', [UserController::class, 'destroy']);
});

Route::prefix('youtube')->middleware('auth:api')->group(function () {
    Route::get('auth-url',   [YouTubeAuthController::class, 'getAuthUrl']);
    Route::get('callback',   [YouTubeAuthController::class, 'callback']);
    Route::post('upload',    [YouTubeUploadController::class, 'upload']);
});