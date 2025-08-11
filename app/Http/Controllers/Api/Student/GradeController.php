<?php
namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Services\GradeService;
use App\Traits\ApiResponse;

class GradeController extends Controller
{
    use ApiResponse;
    protected $service;

    public function __construct(GradeService $service) {

        $this->service = $service;
    }

    public function myGrades() {
        $grades = $this->service->listGradesForStudent(auth()->id());
        return $this->successResponse($grades, 'Your grades fetched successfully.');
    }
}
