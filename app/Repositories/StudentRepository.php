<?php
namespace App\Repositories;
use App\Models\StudentProfile;
use App\Models\User;
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
    public function attachParentToStudent($parentId)
    {
        $studentId = auth()->user()->id;
        // dd($studentId,$parentId);
        $student = User::where('id',$studentId)->first();
        $parent = User::where('id',$parentId)->first();

        if (!$student || !$parent) {
            return false;
        }

        if ($student->role != 'student') {
            return false;
        }
        $parent->students()->attach($studentId);
        return true;
    }

}