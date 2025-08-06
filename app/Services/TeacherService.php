<?php
namespace App\Services;
use app\Repositories\TeacherRepository;

class TeacherService
{
    public $TeacherRep;
    public function __construct(TeacherRepository $TeacherRep)
    {
        $this->TeacherRep = $TeacherRep;
    }
    public function index()
    {
        return $this->TeacherRep->index();
    }

    public function show($userId)
    {
        return $this->TeacherRep->show($userId);
    }

    public function update($userId, $data)
    {
        return $this->TeacherRep->update($userId, $data);
    }

    public function delete($userId)
    {
        return $this->TeacherRep->delete($userId);
    }
}