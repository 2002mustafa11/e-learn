<?php
namespace App\Http\Controllers\Api\Teacher;

use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnswerRequest;
use App\Services\AnswerService;

class AnswerController extends Controller {
    use ApiResponse;
    protected $service;

    public function __construct(AnswerService $service) {
        $this->service = $service;
    }

    public function store(AnswerRequest $request) {
        $answer = $this->service->addAnswer($request->validated());
        return $this->successResponse($answer, 'Answer added successfully.', 201);
    }

    public function destroy($id) {
        $this->service->removeAnswer($id);
        return $this->successResponse([], 'Answer deleted successfully.');
    }
}
