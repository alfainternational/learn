<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * تسجيل مستخدم جديد
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'subscription_tier' => 'free',
            'ai_credits_remaining' => 3,
            'ai_credits_reset_at' => now()->addMonth(),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الحساب بنجاح',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        $user->update(['last_login_at' => now()]);
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * بيانات المستخدم الحالي
     */
    public function me(Request $request)
    {
        $user = $request->user()->load([
            'currentSubscription',
            'marketingPlans' => fn($q) => $q->latest()->limit(5),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'stats' => [
                    'total_plans' => $user->marketingPlans()->count(),
                    'completed_plans' => $user->marketingPlans()->where('completion_percentage', 100)->count(),
                    'ai_credits_remaining' => $user->ai_credits_remaining,
                    'ai_credits_unlimited' => $user->subscription_tier !== 'free',
                ],
            ],
        ]);
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح',
        ]);
    }
}
