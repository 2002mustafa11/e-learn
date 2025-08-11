<?php

namespace App\Services;
use App\Repositories\CorrectionRepository;

class CorrectionService
{
    protected $repo;

    public function __construct(CorrectionRepository $repo)
    {
        $this->repo = $repo;
    }

    public function addCorrection(array $data)
    {
        return $this->repo->create($data);
    }

    public function getCorrectionsForStudentExam($examId, $studentId)
    {
        return $this->repo->getByExamAndStudent($examId, $studentId);
    }
}
