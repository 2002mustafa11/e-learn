<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\TeacherProfile;

class UserIsTeacher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $teacher = TeacherProfile::where('user_id',auth()->id())->first();
        if (!$teacher) {
            return response()->json([
                'status'  => false,
                'message' => 'Access denied. Only teachers can perform this action.',
            ], 403);
        }
        return $next($request);
    }
}
