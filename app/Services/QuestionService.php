<?php
namespace App\Services;
use App\Repositories\QuestionRepository;

class QuestionService {
    protected $repo;
    public function __construct(QuestionRepository $repo) { $this->repo = $repo; }
    public function create(array $data) { return $this->repo->create($data); }
    public function delete($id) { return $this->repo->delete($id); }
}
