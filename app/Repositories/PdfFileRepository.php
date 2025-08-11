<?php
namespace App\Repositories;

use App\Models\PdfFile;

class PdfFileRepository
{
    public function create(array $data)
    {
        return PdfFile::create($data);
    }
    public function find($id)
    {
        return PdfFile::find($id);
    }
    public function delete($id)
    {
        return PdfFile::destroy($id);
    }
}
