<?php

namespace App\Repositories;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\ParentProfile;
use Illuminate\Support\Facades\DB;
class AuthRepository
{
    public function register(array $data): User
    {

        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'       => $data['name'],
                'email'      => $data['email'],
                'phone'      => $data['phone'] ?? null,
                'password'   => $data['password'],
                'role'       => $data['role'] ?? 'student',
                'last_login_at'=> now(),
            ]);

            switch ($user->role) {
                case 'student':
                    StudentProfile::create([
                        'user_id'      => $user->id,
                        'grade_level'  => $data['grade_level'] ?? null,
                        'birth_date'   => $data['birth_date'] ?? null,
                    ]);
                    break;

                case 'teacher':
                    TeacherProfile::create([
                        'user_id'         => $user->id,
                        'specialization'  => $data['specialization'] ?? null,
                        'experience_years'=> $data['experience_years'] ?? 0,
                        'bio'             => $data['bio'] ?? null,
                    ]);
                    break;

                case 'parent':
                    ParentProfile::create([
                        'user_id'       => $user->id,
                        'relation_type' => $data['relation_type'] ?? null,
                        'job'           => $data['job'] ?? null,
                    ]);
                    break;
            }

            return $user;
        });
    }

    public function login(User $user)
    {
        $user->last_login_at = now();
        $user->save();

        return $user;
    }

    public function logout(User $user)
    {
        $user->last_login_at = null;
        $user->save();

        JWTAuth::invalidate(JWTAuth::getToken());
    }
}
