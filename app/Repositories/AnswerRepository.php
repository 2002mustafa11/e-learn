<?php

namespace App\Repositories;
use App\Models\Answer;

class AnswerRepository {
    public function create(array $data) {
        return Answer::create($data);
    }
    public function delete($id) {
        return Answer::destroy($id);
    }
}
