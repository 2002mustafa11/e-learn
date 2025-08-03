<?php
namespace App\Repositories;
use App\Models\StudentProfile;

class StudentRepository
{
    public function index(){
        return StudentProfile::whereHas('user', function ($query) {
            $query->where('role', 'student');
        })->with('user')->get();
    }

    public function show($userId){
        return StudentProfile::where('user_id', $userId)->with('user')->first();
    }

    public function update($userId,$data){
        $student = StudentProfile::where('user_id', $userId)->with('user')->first();
        $student->update($data);
        $student->user->update($data['user']);
        return $student;
    }

    public function delete($userId){
        $student = StudentProfile::where('user_id', $userId)->with('user')->first();
        $student->delete();
        $student->user()->delete();
        return true;
    }
}