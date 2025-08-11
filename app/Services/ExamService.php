<?php
namespace App\Services;
use App\Repositories\ExamRepository;

class ExamService {
    protected $repo;
    public function __construct(ExamRepository $repo) { $this->repo = $repo; }
    public function create(array $data) { return $this->repo->create($data); }
    public function getById($id) { return $this->repo->find($id); }
    public function delete($id) { return $this->repo->delete($id); }
}

