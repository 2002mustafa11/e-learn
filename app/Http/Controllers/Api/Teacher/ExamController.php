<?php
namespace App\Http\Controllers\Api\Teacher;

use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExamRequest;
use App\Services\ExamService;
class ExamController extends Controller {
    use ApiResponse;
    protected $service;
    public function __construct(ExamService $service) { $this->service = $service; }

    public function store(ExamRequest $req) {
        $exam = $this->service->create($req->validated());
        return $this->successResponse($exam, 'Exam created.', 201);
    }

    public function show($id) {
        $exam = $this->service->getById($id);
        return $exam
            ? $this->successResponse($exam, 'OK')
            : $this->errorResponse('Not found', [], 404);
    }

    public function destroy($id) {
        $this->service->delete($id);
        return $this->successResponse([], 'Deleted.');
    }
}

