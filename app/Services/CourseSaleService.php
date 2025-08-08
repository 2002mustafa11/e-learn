<?php

namespace App\Services;

use App\Repositories\CourseSaleRepository;
use Illuminate\Support\Facades\DB;

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
        if ($existing && $existing->payment_status === 'paid') {
            throw new \Exception('You have already purchased this course.');
        }

        return DB::transaction(function () use ($userId, $courseId, $price, $paymentMethod) {
            return $this->repo->create([
                'user_id'        => $userId,
                'course_id'      => $courseId,
                'price'          => $price,
                'payment_status' => 'pending',
                'payment_method' => $paymentMethod,
                'purchased_at'   => now(),
            ]);
        });
    }

    public function markAsPaid($saleId)
    {
        return $this->repo->updateStatus($saleId, 'paid');
    }
}
