<?php

namespace App\Services;
use App\Repositories\AnswerRepository;

class AnswerService {
    protected $repo;

    public function __construct(AnswerRepository $repo) {
        $this->repo = $repo;
    }

    public function addAnswer(array $data) {
        return $this->repo->create($data);
    }

    public function removeAnswer($id) {
        return $this->repo->delete($id);
    }
}
