<?php
namespace App\Repositories;
use App\Models\Correction;

class CorrectionRepository
{
    public function create(array $data)
    {
        $data['student_id'] = auth()->id();
        return Correction::create($data);
    }

    public function getByExamAndStudent($examId, $studentId)
    {
        return Correction::where('exam_id', $examId)
                         ->where('student_id', $studentId)
                         ->with('question:id,score')
                         ->get();
    }
}
