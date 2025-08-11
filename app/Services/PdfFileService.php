<?php
namespace App\Services;

use App\Repositories\PdfFileRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PdfFileService
{
    protected $repo;

    public function __construct(PdfFileRepository $repo)
    {
        $this->repo = $repo;
    }

    public function uploadPdf($userId, array $data)
    {
        $file = $data['file'];
        $path = $file->store('pdfs', 'public');

        $pdfData = [
            'id'         => Str::uuid(),
            'user_id'    => $userId,
            'lesson_id'  => $data['lesson_id'],
            'file_path'  => $path,
            'file_name'  => $file->getClientOriginalName(),
            'file_size'  => $file->getSize(),
            'page_count' => $data['page_count'],
        ];

        return $this->repo->create($pdfData);
    }

    public function removePdf($id)
    {
        $pdf = $this->repo->find($id);
        if ($pdf && Storage::disk('public')->exists($pdf->file_path)) {
            Storage::disk('public')->delete($pdf->file_path);
        }
        return $this->repo->delete($id);
    }

}
