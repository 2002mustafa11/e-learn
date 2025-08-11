<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Teacher\YouTube\YouTubeAuthController;
use App\Http\Controllers\Api\Teacher\YouTube\YouTubeUploadController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Teacher\CourseController;
use App\Http\Controllers\Api\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Api\Teacher\LessonController;
use App\Http\Controllers\Api\Teacher\PdfFileController;
use App\Http\Controllers\Api\Teacher\ExamController;
use App\Http\Controllers\Api\Teacher\QuestionController;
use App\Http\Controllers\Api\Teacher\AnswerController;
use App\Http\Controllers\Api\Teacher\GradeController;
use App\Http\Controllers\Api\Student\CourseReviewController;
use App\Http\Controllers\Api\Student\GradeController as StudentGradeController;

Route::prefix('teacher')->middleware(['auth:api','teacher'])->group(function () {
    Route::post('grades', [GradeController::class, 'save']);
    Route::get('grades/exam/{examId}', [GradeController::class, 'listByExam']);
});

Route::prefix('student')->middleware(['auth:api','student'])->group(function () {
    Route::get('grades', [StudentGradeController::class, 'myGrades']);
});


Route::middleware('auth:api', 'student')->group(function () {
    Route::post('reviews', [CourseReviewController::class, 'store']);
    Route::delete('reviews/{courseId}', [CourseReviewController::class, 'destroy']);
});

Route::post('answers', [AnswerController::class, 'store']);
Route::delete('answers/{id}', [AnswerController::class, 'destroy']);


Route::apiResource('exams', ExamController::class)->only(['store','show','destroy']);
Route::post('questions', [QuestionController::class,'store']);
Route::delete('questions/{id}', [QuestionController::class,'destroy']);


Route::prefix('teacher')->middleware(['auth:api', 'teacher'])->group(function () {
    Route::post('pdf-files', [PdfFileController::class, 'store']);
    Route::delete('pdf-files/{id}', [PdfFileController::class, 'destroy']);
});


Route::prefix('teacher')->middleware(['auth:api', 'teacher'])->group(function () {
    Route::apiResource('lessons', LessonController::class);
});

Route::prefix('teacher')->middleware(['auth:api', 'teacher'])->group(function () {
    Route::apiResource('courses', CourseController::class);
});

Route::prefix('student')->middleware(['auth:api', 'student'])->group(function () {
    Route::get('courses', [StudentCourseController::class, 'index']);
    Route::get('courses/{course}', [StudentCourseController::class, 'show']);
    Route::post('courses/{course}/purchase', [StudentCourseController::class, 'purchase']);
});

Route::middleware(['auth:api', 'admin'])->apiResource('categories', CategoryController::class);

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout']);
});

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