<?php

namespace App\Services;
use App\Repositories\GradeRepository;
use App\Repositories\CorrectionRepository;
class GradeService
{
    protected $repo;
    protected $correctionRepository;

    public function __construct(GradeRepository $repo ,CorrectionRepository $correctionRepository) {
        $this->repo = $repo;
        $this->correctionRepository = $correctionRepository;
    }
    public function saveGrade(array $data)
    {
        $data['student_id'] = auth()->id();
        $existing = $this->repo->findByStudentExam($data['student_id'], $data['exam_id']);
        if ($existing) {
            return $existing;
        }

        $corrections = $this->correctionRepository->getByExamAndStudent(
            $data['exam_id'],
            $data['student_id']
        );

        $totalScore = $corrections->sum(function ($correction) {
            return $correction->is_correct && $correction->question
                ? (float) $correction->question->score
                : 0;
        });

        $data['score'] = $totalScore;

        return $this->repo->create($data);
    }


    public function listGradesForExam($examId) {
        return $this->repo->getByExam($examId);
    }

    public function listGradesForStudent($studentId) {
        return $this->repo->getByStudent($studentId);
    }
}
