<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\GradeRequest;
use App\Services\GradeService;
class GradeController extends Controller
{
    use ApiResponse;
    protected $service;

    public function __construct(GradeService $service) {

        $this->service = $service;
    }

    public function save(GradeRequest $req) {
        $grade = $this->service->saveGrade($req->validated());
        return $this->successResponse($grade, 'Grade saved successfully.');
    }

    public function listByExam($examId) {
        $grades = $this->service->listGradesForExam($examId);
        return $this->successResponse($grades, 'Grades fetched for exam.');
    }
}
