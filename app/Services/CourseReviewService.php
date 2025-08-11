<?php

namespace App\Services;
use App\Repositories\CourseReviewRepository;

class CourseReviewService
{
    protected $repo;

    public function __construct(CourseReviewRepository $repo)
    {
        $this->repo = $repo;
    }

    public function saveReview($userId, array $data)
    {
        $existing = $this->repo->findByUserAndCourse($userId, $data['course_id']);
        if ($existing) {
            return $this->repo->update($existing->id, array_merge($data, ['user_id' => $userId]));
        }
        return $this->repo->create(array_merge($data, ['user_id' => $userId]));
    }

    public function deleteReview($userId, $courseId)
    {
        $existing = $this->repo->findByUserAndCourse($userId, $courseId);
        if ($existing) {
            return $this->repo->delete($existing->id);
        }
        throw new \Exception('Review not found.');
    }
}
