<?php

namespace App\Repositories;
use App\Models\Question;

class QuestionRepository {
    public function create(array $data)
    {
        return Question::create($data);
    }

    public function ByExam($exam_id)
    {
        return Question::where('exam_id',$exam_id)->with('answers','exam')->frist();
    }

    public function find($id)
    {
        return Question::with([
            'answers' => function ($query) {
                $query->where('is_correct', 1);
            },
            'exam'
        ])->find($id);

    }
    public function delete($id)
    {
        return Question::destroy($id);
    }
}
