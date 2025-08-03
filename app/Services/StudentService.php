<?php
namespace App\Services;
use app\Repositories\StudentRepository;

class StudentService
{
    public $StudentRepository;
    public function __construct(StudentRepository $StudentRepository)
    {
        $this->StudentRepository = $StudentRepository;
    }
    public function index()
    {
        return $this->StudentRepository->index();
    }

    public function show($userId)
    {
        return $this->StudentRepository->show($userId);
    }

    public function update($userId, $data)
    {
        return $this->StudentRepository->update($userId, $data);
    }

    public function delete($userId)
    {
        return $this->StudentRepository->delete($userId);
    }
}