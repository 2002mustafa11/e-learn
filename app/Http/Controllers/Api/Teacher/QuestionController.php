<?php
namespace App\Http\Controllers\Api\Teacher;

use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;
use App\Services\QuestionService;

class QuestionController extends Controller {
    use ApiResponse;
    protected $service;
    public function __construct(QuestionService $service) { $this->service = $service; }

    public function store(QuestionRequest $req) {
        $q = $this->service->create($req->validated());
        return $this->successResponse($q, 'Question created.', 201);
    }

    public function destroy($id) {
        $this->service->delete($id);
        return $this->successResponse([], 'Deleted.');
    }
}
