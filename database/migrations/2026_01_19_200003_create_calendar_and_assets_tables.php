<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // calendar_events - أحداث التقويم
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('marketing_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type'); // campaign_start, campaign_end, content_publish, meeting, deadline, holiday, custom
            $table->string('color')->nullable();
            
            $table->datetime('start_at');
            $table->datetime('end_at')->nullable();
            $table->boolean('all_day')->default(false);
            
            $table->string('recurrence')->nullable(); // none, daily, weekly, monthly, yearly
            $table->json('recurrence_settings')->nullable();
            
            $table->boolean('is_reminder_enabled')->default(true);
            $table->integer('reminder_minutes_before')->default(30);
            
            $table->timestamps();
            $table->softDeletes();
        });

        // assets - مكتبة الأصول
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('name');
            $table->string('type'); // image, video, document, audio
            $table->string('mime_type');
            $table->string('path');
            $table->string('url');
            $table->string('thumbnail_url')->nullable();
            $table->bigInteger('size'); // bytes
            
            $table->json('metadata')->nullable(); // dimensions, duration, etc
            $table->json('tags')->nullable();
            $table->string('folder')->nullable();
            
            $table->integer('usage_count')->default(0);
            $table->json('performance_data')->nullable(); // أداء الأصل في الحملات
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
        Schema::dropIfExists('calendar_events');
    }
};
