<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = config('subscription.plans');
        return response()->json(['success' => true, 'data' => $plans]);
    }

    public function current(Request $request)
    {
        $user = $request->user();
        $subscription = $user->currentSubscription;

        return response()->json([
            'success' => true,
            'data' => [
                'tier' => $user->subscription_tier,
                'subscription' => $subscription,
                'limits' => config("subscription.plans.{$user->subscription_tier}.limits"),
                'features' => config("subscription.plans.{$user->subscription_tier}.features"),
            ]
        ]);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'tier' => 'required|in:basic,pro,enterprise',
            'payment_method_id' => 'required|string'
        ]);

        // Mock subscription logic for now
        // In real app, we would interface with Stripe/Cashier here
        
        $user = $request->user();
        
        // Update user tier
        $user->update(['subscription_tier' => $request->tier]);
        
        // Create subscription record
        $user->subscriptions()->create([
            'plan_type' => $request->tier,
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'price' => config("subscription.plans.{$request->tier}.price"),
        ]);

        return response()->json(['success' => true, 'message' => 'تم الاشتراك بنجاح']);
    }

    public function cancel(Request $request)
    {
        // Mock cancellation
        $request->user()->currentSubscription()->update(['canceled_at' => now()]);
        return response()->json(['success' => true, 'message' => 'تم إلغاء التجديد التلقائي']);
    }

    public function resume(Request $request)
    {
        // Mock resume
        $request->user()->currentSubscription()->update(['canceled_at' => null]);
        return response()->json(['success' => true, 'message' => 'تم استئناف الاشتراك']);
    }
}
