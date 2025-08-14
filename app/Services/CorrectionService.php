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
            $data['is_correct'] = ($data['correct_answer'] == $data['student_answer']) ? 1 : 0;
        } elseif ($question->type == 'essay') {

            $fastApiUrl = 'https://12e43de9fd6d.ngrok-free.app/evaluate';

            try {
                $response = \Illuminate\Support\Facades\Http::post($fastApiUrl, [
                    'question'       => $question->title,
                    'model_answer'   => $question->answers[0]->answer,
                    'student_answer' => $data['student_answer']
                ]);

                if ($response->successful()) {
                    $result = $response->json();
                    $data['is_correct'] = ($result['score'] ?? 0) > (($question->score ?? 0) / 2) ? 1 : 0;
                    $data['correct_answer'] = $result['feedback'] ?? '';
                } else {
                    $data['is_correct'] = 0;
                    $data['correct_answer'] = 'Error connecting to the debugging service.';
                }
            } catch (\Exception $e) {
                $data['is_correct'] = 0;
                $data['correct_answer'] = 'Error connecting to the debugging service: ' . $e->getMessage();
            }
        }
        // dd($data);
        return $this->CorrectionRepo->create($data);
    }


    public function getCorrectionsForStudentExam($examId, $studentId)
    {
        return $this->CorrectionRepo->getByExamAndStudent($examId, $studentId);
    }
}
