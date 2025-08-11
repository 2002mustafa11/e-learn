<?php
// app/Http/Controllers/Api/Teacher/LessonController.php
namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\LessonRequest;
use App\Services\LessonService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    use ApiResponse;
    protected LessonService $service;

    public function __construct(LessonService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $lessons = $this->service->getAllLessons($request->all());
        return $this->successResponse($lessons, 'Lessons fetched.');
    }

    public function store(LessonRequest $request)
    {
        $lesson = $this->service->createLesson($request->validated());
        return $this->successResponse($lesson, 'Lesson created.', 201);
    }

    public function show($id)
    {
        $lesson = $this->service->getLessonById($id);
        if (!$lesson) return $this->errorResponse('Not found.', [], 404);
        return $this->successResponse($lesson, 'Lesson fetched.');
    }

    public function update(LessonRequest $request, $id)
    {
        $lesson = $this->service->getLessonById($id);
        if (!$lesson) return $this->errorResponse('Not found.', [], 404);
        $updated = $this->service->updateLesson($id, $request->validated());
        return $this->successResponse($updated, 'Lesson updated.');
    }

    public function destroy($id)
    {
        $deleted = $this->service->deleteLesson($id);
        return $this->successResponse([], 'Lesson deleted.');
    }
}
