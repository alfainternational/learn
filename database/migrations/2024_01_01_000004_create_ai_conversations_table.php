<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->constrained('marketing_plans')->onDelete('set null');
            $table->string('session_id', 64);
            $table->enum('message_type', ['user', 'assistant']);
            $table->text('message_text');
            $table->json('context')->nullable();
            $table->string('ai_model', 50)->default('gemini-pro');
            $table->integer('tokens_used')->nullable();
            $table->integer('processing_time_ms')->nullable();
            $table->timestamp('created_at');

            $table->index('session_id');
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_conversations');
    }
};
