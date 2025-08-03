<?php
namespace App\Services;
use app\Repositories\ParentRepository;

class ParentService
{
    public $ParentRepository;
    public function __construct(ParentRepository $ParentRepository)
    {
        $this->ParentRepository = $ParentRepository;
    }
    public function index()
    {
        return $this->ParentRepository->index();
    }

    public function show($userId)
    {
        return $this->ParentRepository->show($userId);
    }

    public function update($userId, $data)
    {
        return $this->ParentRepository->update($userId, $data);
    }

    public function delete($userId)
    {
        return $this->ParentRepository->delete($userId);
    }
}