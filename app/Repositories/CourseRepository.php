<?php

namespace App\Repositories;

use App\Models\Course;

class CourseRepository
{
    public function allWithFilters($filters)
    {
        $query = Course::with('categories', 'user');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        if (!empty($filters['language'])) {
            $query->where('language', $filters['language']);
        }

        if (!empty($filters['skill_level'])) {
            $query->where('skill_level', $filters['skill_level']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['category_id'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        $perPage = $filters['per_page'] ?? 10;
        return $query->paginate($perPage);
    }

    public function all()
    {
        return Course::with('categories', 'user')->get();
    }

    public function findWithLessons($courseId, $allLessons = false)
    {
        return Course::with([
            'categories',
            'user',
            'lessons' => function ($q) use ($allLessons) {
                if (!$allLessons) {
                    $q->where('is_free', 1);
                }
                $q->orderBy('order');
            }
        ])->findOrFail($courseId);
    }
    
    public function findOrFail($courseId)
    {
        return Course::findOrFail($courseId);
    }

    public function find($id)
    {
        return Course::with(['categories', 'user', 'lessons' => function ($q) {
            $q->orderBy('order');
        }])->findOrFail($id);
    }

    public function create(array $data)
    {
        $course = Course::create($data);
        if (isset($data['categories_id'])) {
            $course->categories()->sync($data['categories_id']);
        }
        return $course;
    }

    public function update($id, array $data)
    {
        $course = Course::findOrFail($id);
        $course->update($data);
        if (isset($data['categories'])) {
            $course->categories()->sync($data['categories']);
        }
        return $course;
    }

    public function delete($id)
    {
        $course = Course::findOrFail($id);
        $course->categories()->detach();
        $course->delete();
        return true;
    }
}
