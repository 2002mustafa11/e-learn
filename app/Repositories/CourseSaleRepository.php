<?php

namespace App\Repositories;

use App\Models\CourseSale;

class CourseSaleRepository
{
    public function create(array $data)
    {
        return CourseSale::create($data);
    } 

    public function findByUserAndCourse($userId, $courseId)
    {
        return CourseSale::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();
    }

    public function updateStatus($saleId, $status)
    {
        return CourseSale::where('id', $saleId)->update(['payment_status' => $status]);
    }
}
