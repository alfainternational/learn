<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAICredits
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح. يرجى تسجيل الدخول.',
            ], 401);
        }

        // التحقق من رصيد AI
        if (!$user->hasAICredits()) {
            return response()->json([
                'success' => false,
                'message' => 'لقد استنفدت رصيد AI الخاص بك. قم بالترقية للاشتراك المدفوع للحصول على رصيد غير محدود.',
                'code' => 'NO_AI_CREDITS',
                'data' => [
                    'credits_remaining' => $user->ai_credits_remaining,
                    'credits_reset_at' => $user->ai_credits_reset_at,
                    'upgrade_url' => url('/api/v1/pricing'),
                ],
            ], 403);
        }

        return $next($request);
    }
}
