<?php
namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseCourseRequest;
use App\Services\CourseSaleService;
use App\Traits\ApiResponse;

class CoursePurchaseController extends Controller
{
    use ApiResponse;

    protected $service;

    public function __construct(CourseSaleService $service)
    {
        $this->service = $service;
    }

    public function purchase(PurchaseCourseRequest $request, $courseId)
    {
        try {
            $sale = $this->service->purchaseCourse(
                auth()->id(),
                $courseId,
                $request->price,
                $request->payment_method
            );
            return $this->successResponse($sale, 'Course purchase initiated.', 201);
        } catch (Exception $e) {
            return $this->errorResponse($e->errors(), [], 422);
        }
    }
}
