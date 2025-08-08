<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Services\CourseService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    use ApiResponse;

    protected $service;

    public function __construct(CourseService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'language', 'skill_level', 'per_page']);
        $filters['user_id'] = auth()->id();

        $courses = $this->service->getAllCourses($filters);
        return $this->successResponse($courses, 'Courses fetched successfully.');
    }

    public function store(CourseRequest $request)
    {
        $data = $request->validated();
        // dd($data['categories_id']);
        $course = $this->service->createCourse($data);
        return $this->successResponse($course, 'Course created successfully.', 201);
    }

    public function show(Request $request, $id)
    {
        $course = $this->service->getCourseById($id);

        if ($request->user()->cannot('view', $course)) {
            return $this->errorResponse('Course not found or unauthorized.', [], 404);
        }

        return $this->successResponse($course, 'Course fetched successfully.');
    }

    public function update(CourseRequest $request, $id)
    {
        $course = $this->service->getCourseById($id);

        if ($request->user()->cannot('update', $course)) {
            return $this->errorResponse('Course not found or unauthorized.', [], 404);
        }

        $updated = $this->service->updateCourse($id, $request->validated());
        return $this->successResponse($updated, 'Course updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $course = $this->service->getCourseById($id);

        if ($request->user()->cannot('delete', $course)) {
            return $this->errorResponse('Course not found or unauthorized.', [], 404);
        }

        $this->service->deleteCourse($id);
        return $this->successResponse([], 'Course deleted successfully.');
    }
}