<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $requiredTier = null)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح. يرجى تسجيل الدخول.',
            ], 401);
        }

        // التحقق من صلاحية الاشتراك
        if (!$user->hasActiveSubscription() && $requiredTier !== 'free') {
            return response()->json([
                'success' => false,
                'message' => 'هذه الميزة متاحة للمشتركين المدفوعين فقط.',
                'code' => 'SUBSCRIPTION_REQUIRED',
            ], 403);
        }

        // التحقق من المستوى المطلوب
        if ($requiredTier) {
            $tierHierarchy = ['free' => 0, 'basic' => 1, 'pro' => 2, 'enterprise' => 3];
            $userTierLevel = $tierHierarchy[$user->subscription_tier] ?? 0;
            $requiredTierLevel = $tierHierarchy[$requiredTier] ?? 0;

            if ($userTierLevel < $requiredTierLevel) {
                return response()->json([
                    'success' => false,
                    'message' => "هذه الميزة تتطلب اشتراك {$requiredTier} أو أعلى.",
                    'code' => 'HIGHER_TIER_REQUIRED',
                ], 403);
            }
        }

        return $next($request);
    }
}
