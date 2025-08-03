<?php

namespace App\Repositories;

use App\Models\TeacherProfile;

class TeacherRepository
{
    public function index()
    {
        return TeacherProfile::whereHas('user', function ($query) {
            $query->where('role', 'teacher');
        })->with('user')->get();;
    }

    public function show($userId)
    {
        return TeacherProfile::where('user_id', $userId)->with('user')->first();
    }

    public function update($userId, $data)
    {
        $teacher = TeacherProfile::where('user_id', $userId)->with('user')->first();
        $teacher->update($data);
        $teacher->user->update($data['user']);
        return $teacher;
    }

    public function delete($userId)
    {
        $teacher = TeacherProfile::where('user_id', $userId)->with('user')->first();
        $teacher->delete();
        $teacher->user()->delete();
        return true;
    }
}
