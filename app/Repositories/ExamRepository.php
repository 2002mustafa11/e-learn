<?php

namespace App\Repositories;
use App\Models\Exam;

class ExamRepository {
    public function create(array $data)
    {
        return Exam::create($data);
    }
    public function find($id)
    {
        return Exam::find($id);
    }
    public function delete($id)
    {
        return Exam::destroy($id);
    }
}

