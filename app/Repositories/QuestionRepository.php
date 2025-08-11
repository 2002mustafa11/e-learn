<?php

namespace App\Repositories;
use App\Models\Question;

class QuestionRepository {
    public function create(array $data) { return Question::create($data); }
    public function find($id) { return Question::find($id); }
    public function delete($id) { return Question::destroy($id); }
}
