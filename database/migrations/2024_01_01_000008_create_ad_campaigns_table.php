<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertiser_id')->constrained('users')->onDelete('cascade');
            $table->string('campaign_name');
            $table->enum('status', ['draft', 'pending_review', 'active', 'paused', 'completed', 'rejected'])->default('draft');
            $table->enum('ad_type', ['banner', 'native', 'sponsored_template', 'sponsored_suggestion']);

            // Ad Content
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('image_url')->nullable();
            $table->string('cta_text', 50)->nullable();
            $table->text('cta_url');

            // Targeting
            $table->json('target_subscription_tiers')->nullable();
            $table->json('target_industries')->nullable();
            $table->json('target_locations')->nullable();
            $table->json('ad_placements')->nullable();

            // Budget & Scheduling
            $table->decimal('budget_total', 10, 2);
            $table->decimal('budget_spent', 10, 2)->default(0);
            $table->decimal('cost_per_click', 6, 2)->nullable();
            $table->decimal('cost_per_impression', 6, 2)->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');

            // Statistics
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('conversions')->default(0);

            // Review
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->index(['status', 'starts_at', 'ends_at']);
            $table->index('advertiser_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_campaigns');
    }
};
