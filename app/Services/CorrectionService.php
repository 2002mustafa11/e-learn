<?php

namespace App\Services;
use App\Repositories\CorrectionRepository;
use App\Repositories\QuestionRepository;

class CorrectionService
{
    protected $CorrectionRepo;
    protected $QuestionRepo;

    public function __construct(CorrectionRepository $CorrectionRepo, QuestionRepository $QuestionRepo)
    {
        $this->CorrectionRepo = $CorrectionRepo;
        $this->QuestionRepo = $QuestionRepo;
    }

    public function addCorrection(array $data)
    {
        $question = $this->QuestionRepo->find($data['question_id']);

        if ($question->type == 'true_false' || $question->type == 'multiple_choice') {
            $data['correct_answer'] = $question->answers[0]->answer;
            if($data['correct_answer'] == $data['student_answer']){
                $data['is_correct']=1;
            }else{
                $data['is_correct']=0;
            }
        }elseif($question->type == 'essay'){

        }
        // dd($question,$data);
        return $this->CorrectionRepo->create($data);
    }

    public function getCorrectionsForStudentExam($examId, $studentId)
    {
        return $this->CorrectionRepo->getByExamAndStudent($examId, $studentId);
    }
}
