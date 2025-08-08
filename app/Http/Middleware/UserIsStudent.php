<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\StudentProfile;
class UserIsStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $student = StudentProfile::where('user_id',auth()->id())->first();
        if (!$student) {
            return response()->json([
                'status'  => false,
                'message' => 'Access denied. Only students can perform this action.',
            ], 403);
        }
        return $next($request);
    }
}
