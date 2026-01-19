<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MarketingPlan;
use App\Models\Subscription;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'total_users' => User::count(),
                    'active_subscriptions' => Subscription::where('status', 'active')->count(),
                    'total_plans' => MarketingPlan::count(),
                    'revenue_this_month' => Subscription::whereMonth('created_at', now()->month)->sum('amount'),
                ],
                'recent_users' => User::latest()->limit(5)->get(),
                'recent_plans' => MarketingPlan::with('user')->latest()->limit(5)->get(),
            ]
        ]);
    }
}
