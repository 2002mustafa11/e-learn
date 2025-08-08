<?php

namespace App\Services;

use App\Repositories\CourseSaleRepository;
use Illuminate\Validation\ValidationException;
class CourseSaleService
{
    protected $repo;

    public function __construct(CourseSaleRepository $repo)
    {
        $this->repo = $repo;
    }

    public function purchaseCourse($userId, $courseId, $price, $paymentMethod)
    {
        $existing = $this->repo->findByUserAndCourse($userId, $courseId);
        if ($existing) {
            throw ValidationException::withMessages([
                'course' => 'You have already purchased this course.',
            ]);
        }

            return $this->repo->create([
                'user_id'        => $userId,
                'course_id'      => $courseId,
                'price'          => $price,
                'payment_status' => 'pending',
                'payment_method' => $paymentMethod,
                'purchased_at'   => now(),
            ]);
    }

    public function markAsPaid($saleId)
    {
        return $this->repo->updateStatus($saleId, 'paid');
    }
}
