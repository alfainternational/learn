<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->enum('status', ['draft', 'in_progress', 'completed', 'archived'])->default('draft');
            $table->year('year');

            // Business Data
            $table->string('business_name')->nullable();
            $table->string('industry', 100)->nullable();
            $table->json('target_audience')->nullable();
            $table->text('marketing_goal')->nullable();
            $table->decimal('budget_monthly', 10, 2)->nullable();

            // Progress Tracking
            $table->tinyInteger('completion_percentage')->default(0);
            $table->tinyInteger('ai_score')->default(0);
            $table->timestamp('last_ai_review_at')->nullable();
            $table->json('sections_completed')->nullable();

            // Sharing
            $table->boolean('is_public')->default(false);
            $table->string('share_token', 64)->unique()->nullable();
            $table->integer('view_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('share_token');
            $table->index('industry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_plans');
    }
};
