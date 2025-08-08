<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Services\CourseService;
use App\Services\CourseSaleService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    use ApiResponse;

    protected $service;
    protected $CourseSaleService;

    public function __construct(CourseService $service ,CourseSaleService $CourseSaleService)
    {
        $this->service = $service;
        $this->CourseSaleService = $CourseSaleService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'language', 'skill_level', 'category_id', 'per_page']);
        $courses = $this->service->getAllCourses($filters);
        return $this->successResponse($courses, 'Courses fetched successfully.');
    }

    public function show($id)
    {
        $course = $this->service->getCourseById($id);

        if (!$course) {
            return $this->errorResponse('Course not found.', [], 404);
        }

        return $this->successResponse($course, 'Course fetched successfully.');
    }

    public function purchase(Request $request, $courseId)
    {
        try {
            $sale = $this->CourseSaleService->purchaseCourse(
                auth()->id(),
                $courseId,
                $request->price,
                $request->payment_method
            );
            return $this->successResponse($sale, 'Course purchase initiated.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), [], 400);
        }
    }
    public function enroll($id)
    {
        // هنا ممكن نعمل منطق التسجيل في الكورس
        // مثال: EnrollmentService->enrollStudent(auth()->id(), $id);
        return $this->successResponse([], 'Enrolled successfully.');
    }
}
