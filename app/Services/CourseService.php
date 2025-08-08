<?php

namespace App\Services;

use App\Repositories\CourseRepository;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    use Intervention\Image\ImageManager;
    use Intervention\Image\Drivers\Gd\Driver;

class CourseService
{
    protected $repo;

    public function __construct(CourseRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAllCourses(array $filters = [])
    {
        return $this->repo->allWithFilters($filters);
    }
    public function getAll()
    {
        return $this->repo->all();
    }

    public function getCourseById($id)
    {
        return $this->repo->find($id);
    }

    public function createCourse(array $data)
    {
        $data['user_id'] = auth()->id();

        if (isset($data['image'])) {
            $data['image'] = $this->uploadImage($data['image']);
        }
        return $this->repo->create($data);
    }

    public function updateCourse($id, array $data)
    {
        $course = $this->repo->find($id);
        if (isset($data['image'])) {
            $data['image'] = $this->uploadImage($data['image'], $course->image);
        }
        return $this->repo->update($id, $data);
    }

    public function deleteCourse($id)
    {
        $course = $this->repo->find($id);

        if (!$course) {
            throw new \Exception('Course not found.');
        }

        if ($course->image && Storage::disk('public')->exists($course->image)) {
            Storage::disk('public')->delete($course->image);
        }

        return $this->repo->delete($id);
    }


    protected function uploadImage($file, $oldImage = null)
    {
        if ($oldImage && Storage::disk('public')->exists($oldImage)) {
            Storage::disk('public')->delete($oldImage);
        }

        $manager = new ImageManager(new Driver());

        $image = $manager->read($file->getPathname());

        $image->resize(height: 800);

        // watermark
        // $image->place('images/watermark.png');

        $encoded = $image->toJpeg(quality: 75);

        $filename = 'courses/' . Str::uuid() . '.jpg';

        Storage::disk('public')->put($filename, $encoded->toString());

        return $filename;
    }

}
