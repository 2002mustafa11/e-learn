<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use app\Models\User;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
               $students = User::factory()
               ->count(10)
               ->student()
               ->create()
               ->each(function ($student) {
                   $student->studentProfile()->create([
                       'grade_level' => 'Grade ' . rand(6, 12),
                       'birth_date' => now()->subYears(rand(13, 18))->format('Y-m-d'),
                   ]);
               });

           $parents = User::factory()
               ->count(5)
               ->parent()
               ->create()
               ->each(function ($parent) use ($students) {
                   $parent->parentProfile()->create([
                       'relation_type' => 'Father',
                       'job' => 'Engineer',
                   ]);

                   $studentIds = $students->random(2)->pluck('id');
                   $parent->students()->attach($studentIds);
               });

           User::factory()
               ->count(3)
               ->teacher()
               ->create()
               ->each(function ($teacher) {
                   $teacher->teacherProfile()->create([
                       'specialization' => 'Subject ' . rand(1, 5),
                       'experience_years' => rand(1, 20),
                       'bio' => 'Experienced in teaching various subjects.',
                   ]);
               });

           User::factory()
               ->count(2)
               ->admin()
               ->create();
    }
}
