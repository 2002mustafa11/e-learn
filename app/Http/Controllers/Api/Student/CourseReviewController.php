<?php
namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Services\CourseReviewService;
use App\Traits\ApiResponse;
use App\Http\Requests\CourseReviewRequest;


class CourseReviewController extends Controller
{
    use ApiResponse;

    protected $service;

    public function __construct(CourseReviewService $service)
    {
        $this->service = $service;
    }

    public function store(CourseReviewRequest $req)
    {
        $review = $this->service->saveReview(auth()->id(), $req->validated());
        return $this->successResponse($review, 'Review saved successfully.');
    }

    public function destroy($courseId)
    {
        try {
            $this->service->deleteReview(auth()->id(), $courseId);
            return $this->successResponse([], 'Review deleted.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), [], 404);
        }
    }
}
