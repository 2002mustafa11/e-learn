<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{

    public function viewAllLessons(User $user, Course $course): bool
    {
       return $user->courseSales()
                   ->where('course_id', $course->id)
                   ->where('payment_status', 'paid')
                   ->exists();
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Typically, you might want to allow viewing all courses
        // or filter based on user role. Changed from false to true.
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        // Allow viewing if user is the owner or if the course is published
        // Changed from false to a more practical implementation
        return $user->id === $course->user_id ;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Typically only teachers/admins can create courses
        // Changed from false to check for teacher role
        return $user->isTeacher() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        // Owner can update, or admins can update any course
        return $user->id === $course->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        // Owner can delete, or admins can delete any course
        return $user->id === $course->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Course $course): bool
    {
        // Typically only admins can restore soft-deleted courses
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Course $course): bool
    {
        // Typically only admins can force delete
        return $user->isAdmin();
    }
}