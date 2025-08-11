<?php

namespace App\Http\Controllers\Api\Teacher\YouTube;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\YouTube\YouTubeAuthService;
use App\Traits\ApiResponse;

class YouTubeAuthController extends Controller
{
    use ApiResponse;

    private $authService;

    public function __construct(YouTubeAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function getAuthUrl()
    {
        try {
            $authUrl = $this->authService->getAuthUrl();
            return $this->successResponse(
                ['auth_url' => $authUrl],
                'Authentication URL generated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                 'Failed to generate authentication URL',
                ['auth' => $e->getMessage()],
                500
            );
        }
    }

    public function callback(Request $request)
    {
        try {
            $this->validate($request, [
                'code' => 'required|string',
                'state' => 'nullable|string'
            ]);

            $this->authService->handleCallback($request->code);

            return $this->successResponse(
                null,
                'YouTube account connected successfully',
                200
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Invalid data',
                $e->errors(),
                422
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to connect YouTube account',
                ['callback' => $e->getMessage()],
                400
            );
        }
    }
}