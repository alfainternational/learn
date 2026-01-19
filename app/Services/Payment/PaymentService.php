<?php

namespace App\Services\Payment;

use App\Models\User;

class PaymentService
{
    public function createSubscription(User $user, string $planId, string $paymentMethodId)
    {
        // Interface with Stripe/Moyasar
        // For MVP/Demo, we simulate success
        
        return [
            'transaction_id' => 'tx_' . (string) \Illuminate\Support\Str::uuid(),
            'status' => 'success',
            'amount' => config("subscription.plans.{$planId}.price"),
            'currency' => 'SAR',
        ];
    }

    public function cancelSubscription(User $user)
    {
        // Cancel logic
        return true;
    }
}
