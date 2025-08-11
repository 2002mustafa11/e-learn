<?php
namespace App\Repositories;

use App\Models\CourseReview;

class CourseReviewRepository
{
    public function findByUserAndCourse($userId, $courseId)
    {
        return CourseReview::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();
    }

    public function findByCourse($courseId)
    {
        return CourseReview::where('course_id', $courseId)
            ->first();
    }

    public function create(array $data)
    {
        return CourseReview::create($data);
    }

    public function update($id, array $data)
    {
        $rev = CourseReview::findOrFail($id);
        $rev->update($data);
        return $rev;
    }

    public function delete($id)
    {
        return CourseReview::destroy($id);
    }
}
