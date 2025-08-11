<?php
namespace App\Http\Controllers\Api\Teacher;

use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\CorrectionRequest;
use App\Services\CorrectionService;

class CorrectionController extends Controller
{
    use ApiResponse;
    protected $service;

    public function __construct(CorrectionService $service)
    {
        $this->service = $service;
    }

    public function store(CorrectionRequest $request)
    {
        $correction = $this->service->addCorrection($request->validated());
        return $this->successResponse($correction, 'Correction added successfully.', 201);
    }

    public function index($examId, $studentId)
    {
        $data = $this->service->getCorrectionsForStudentExam($examId, $studentId);
        return $this->successResponse($data, 'Corrections fetched successfully.');
    }
}
