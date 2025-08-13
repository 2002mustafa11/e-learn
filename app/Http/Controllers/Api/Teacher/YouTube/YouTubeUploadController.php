<?php

namespace App\Http\Controllers\Api\Teacher\YouTube;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\YouTube\YouTubeUploadService;
use App\Traits\ApiResponse;

class YouTubeUploadController extends Controller
{
    use ApiResponse;

    private $uploadService;

    public function __construct(YouTubeUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function upload(Request $request)
    {
        try {
            $validated = $request->validate([
                'video' => 'required|file|mimetypes:video/*|max:102400',
                'lesson_id' => 'required',
                'title' => 'required|string|max:100',
                'description' => 'nullable|string|max:5000',
                'privacy' => 'required|in:public,unlisted,private',
            ]);

            $video = $this->uploadService->uploadVideo(
                $validated,
                $request->file('video')
            );

            return $this->successResponse(
                [
                    $video
                ],
                'Video uploaded successfully',
                201
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Validation failed',
                $e->errors(),
                422
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to upload video',
                ['upload' => $e->getMessage()],
                500
            );
        }
    }
}