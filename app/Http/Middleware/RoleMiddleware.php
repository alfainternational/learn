<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح. يرجى تسجيل الدخول.',
            ], 401);
        }

        // التحقق من الدور
        if ($user->role !== $role) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالوصول لهذا المورد.',
                'code' => 'FORBIDDEN',
            ], 403);
        }

        return $next($request);
    }
}
