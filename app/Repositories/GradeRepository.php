<?php
namespace App\Repositories;

use App\Models\Grade;
class GradeRepository
{
    public function findByStudentExam($studentId, $examId) {
        return Grade::where('student_id', $studentId)
            ->where('exam_id', $examId)
            ->first();
    }

    public function create(array $data) {
        return Grade::create($data);
    }

    public function update($id, array $data) {
        $grade = Grade::findOrFail($id);
        $grade->update($data);
        return $grade;
    }

    public function getByExam($examId) {
        return Grade::where('exam_id', $examId)->with('student')->get();
    }

    public function getByStudent($studentId) {
        return Grade::where('student_id', $studentId)->with('exam')->get();
    }
}
