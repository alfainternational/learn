<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('marketing_plans')->onDelete('cascade');
            $table->enum('section_type', [
                'personal_card',
                'diagnosis',
                'target_audience',
                'core_message',
                'offer_stack',
                'channels',
                'funnel',
                'followup',
                'metrics',
                'competitor_analysis',
                'content_plan',
                'budget_breakdown'
            ]);
            $table->json('section_data');
            $table->json('ai_suggestions')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['plan_id', 'section_type']);
            $table->unique(['plan_id', 'section_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_sections');
    }
};
