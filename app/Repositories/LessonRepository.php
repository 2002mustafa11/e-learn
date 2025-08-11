<?php
// app/Repositories/LessonRepository.php
namespace App\Repositories;

use App\Models\Lesson;

class LessonRepository
{
    public function all(array $filters = [])
    {
        $query = Lesson::with('course');
        // يمكنك إضافة فلاتر هنا حسب الحاجة
        return $query->orderBy('order')->get();
    }

    public function find($id)
    {
        return Lesson::find($id);
    }

    public function create(array $data)
    {
        return Lesson::create($data);
    }

    public function update($id, array $data)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->update($data);
        return $lesson;
    }

    public function delete($id)
    {
        return Lesson::destroy($id);
    }
}
