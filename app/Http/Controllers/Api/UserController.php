<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Services\ParentService;
use App\Services\TeacherService;
use App\Services\StudentService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use ApiResponse;

    protected $parentService;
    protected $teacherService;
    protected $studentService;

    public function __construct(
        TeacherService $teacherService,
        ParentService $parentService,
        StudentService $studentService
    ) {
        $this->parentService = $parentService;
        $this->teacherService = $teacherService;
        $this->studentService = $studentService;
    }

    public function index(UserRequest $request)
    {
        // dd($request->input('role'));
        try {
            $service = $this->resolveService($request->input('role'));
            $data = $service->index();
            return $this->successResponse($data, 'Data retrieved successfully.');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function show(UserRequest $request, $userId): JsonResponse
    {
        try {
            $service = $this->resolveService($request->input('role'));
            $data = $service->show($userId);
            return $this->successResponse($data, 'User retrieved successfully.');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function update(UserRequest $request, $userId): JsonResponse
    {
        try {
            $service = $this->resolveService($request->input('role'));
            $result = $service->update($userId, $request->validated());
            return $this->successResponse($result, 'User updated successfully.');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function destroy(UserRequest $request, $userId): JsonResponse
    {
        try {
            $service = $this->resolveService($request->input('role'));
            $result = $service->delete($userId);
            return $this->successResponse($result, 'User deleted successfully.');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Resolve the appropriate service based on user role.
     */
    protected function resolveService(string $role)
    {
        return match ($role) {
            'parent' => $this->parentService,
            'teacher' => $this->teacherService,
            'student' => $this->studentService,
            default => throw new \InvalidArgumentException('Invalid role provided.'),
        };
    }
}
