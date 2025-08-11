<?php
namespace App\Http\Controllers\Api\Teacher;

use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PdfFileRequest;
use App\Services\PdfFileService;


class PdfFileController extends Controller
{
    use ApiResponse;
    protected $service;

    public function __construct(PdfFileService $service)
    {
        $this->service = $service;
    }

    public function store(PdfFileRequest $request)
    {
        $pdf = $this->service->uploadPdf(auth()->id(), $request->validated());
        return $this->successResponse($pdf, 'PDF uploaded successfully.', 201);
    }

    public function destroy($id)
    {
        $this->service->removePdf($id);
        return $this->successResponse([], 'PDF deleted successfully.');
    }
}
