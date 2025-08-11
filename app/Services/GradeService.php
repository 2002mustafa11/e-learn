<?php

namespace App\Services;
use App\Repositories\GradeRepository;
class GradeService
{
    protected $repo;

    public function __construct(GradeRepository $repo) {
        $this->repo = $repo;
    }

    public function saveGrade(array $data) {
        $existing = $this->repo->findByStudentExam($data['student_id'], $data['exam_id']);

        if ($existing) {
            return $this->repo->update($existing->id, ['score' => $data['score']]);
        }

        return $this->repo->create($data);
    }

    public function listGradesForExam($examId) {
        return $this->repo->getByExam($examId);
    }

    public function listGradesForStudent($studentId) {
        return $this->repo->getByStudent($studentId);
    }
}
