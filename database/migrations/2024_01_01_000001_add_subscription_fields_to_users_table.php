<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->text('avatar_url')->nullable();
            $table->enum('role', ['admin', 'user', 'advertiser'])->default('user');
            $table->enum('subscription_tier', ['free', 'basic', 'pro', 'enterprise'])->default('free');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->integer('ai_credits_remaining')->default(3);
            $table->timestamp('ai_credits_reset_at')->nullable();
            $table->boolean('onboarding_completed')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->softDeletes();

            $table->index(['subscription_tier', 'subscription_expires_at']);
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'avatar_url', 'role', 'subscription_tier',
                'subscription_expires_at', 'ai_credits_remaining',
                'ai_credits_reset_at', 'onboarding_completed', 'last_login_at'
            ]);
            $table->dropSoftDeletes();
        });
    }
};
