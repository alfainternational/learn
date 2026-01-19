<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MarketingPlan;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Calculate stats
        $plansCount = $user->marketingPlans()->count();
        
        // Calculate average completion with eager loading
        $plans = $user->marketingPlans()->with('sections')->get();
        $totalCompletion = 0;
        foreach ($plans as $plan) {
            $completedSections = $plan->sections->where('is_completed', true)->count();
            $percentage = ($completedSections / 12) * 100;
            $totalCompletion += $percentage;
        }
        $completionAvg = $plansCount > 0 ? round($totalCompletion / $plansCount) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'plans_count' => $plansCount,
                    'completion_avg' => $completionAvg,
                    'ai_credits' => $user->ai_credits_remaining, // Assuming this field exists
                    'subscription_tier' => $user->subscription_tier,
                ],
                // Return top 5 recent plans with eager loading
                'recent_plans' => $user->marketingPlans()
                    ->with('sections')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function($plan) {
                        $completedSections = $plan->sections->where('is_completed', true)->count();
                        $percentage = round(($completedSections / 12) * 100);
                        $plan->completion_percentage = $percentage;
                        return $plan;
                    }),
            ]
        ]);
    }
}
