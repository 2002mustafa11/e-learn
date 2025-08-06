<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ParentRepository;
use App\Services\ParentService;
use App\Repositories\TeacherRepository;
use App\Services\TeacherService;
use App\Repositories\StudentRepository;
use App\Services\StudentService;
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ParentService::class, function ($app) {
            return new ParentService(new ParentRepository());
        });

        $this->app->bind(TeacherService::class, function ($app) {
            return new TeacherService(new TeacherRepository());
        });

        $this->app->bind(StudentService::class, function ($app) {
            return new StudentService(new StudentRepository());
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
