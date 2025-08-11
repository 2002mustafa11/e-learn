<?php

namespace App\Services;

use App\Repositories\LessonRepository;

class LessonService
{
    protected LessonRepository $repo;

    public function __construct(LessonRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAllLessons(array $filters = [])
    {
        return $this->repo->all($filters);
    }

    public function getLessonById($id)
    {
        return $this->repo->find($id);
    }

    public function createLesson(array $data)
    {
        return $this->repo->create($data);
    }

    public function updateLesson($id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function deleteLesson($id)
    {
        return $this->repo->delete($id);
    }
}
