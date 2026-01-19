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
        // campaigns - الحملات الإعلانية للتحليل
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('marketing_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('platform'); // facebook, google, instagram, tiktok, snapchat, twitter, linkedin
            $table->string('campaign_type'); // awareness, traffic, engagement, leads, conversions, sales
            $table->string('objective')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status')->default('active'); // active, paused, completed, draft
            
            // الميزانية
            $table->decimal('planned_budget', 12, 2)->default(0);
            $table->decimal('actual_spend', 12, 2)->default(0);
            $table->string('currency')->default('USD');
            
            // الاستهداف
            $table->json('target_audience')->nullable(); // age, gender, location, interests
            $table->json('targeting_settings')->nullable();
            
            // المحتوى
            $table->string('ad_type')->nullable(); // image, video, carousel, story
            $table->text('ad_copy')->nullable();
            $table->string('cta')->nullable();
            $table->json('creative_assets')->nullable();
            
            // الروابط
            $table->string('landing_page_url')->nullable();
            $table->string('tracking_url')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // campaign_metrics - مؤشرات الأداء اليومية
        Schema::create('campaign_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            
            // مؤشرات الوصول
            $table->bigInteger('impressions')->default(0);
            $table->bigInteger('reach')->default(0);
            $table->bigInteger('frequency')->default(0);
            
            // مؤشرات التفاعل
            $table->bigInteger('clicks')->default(0);
            $table->decimal('ctr', 8, 4)->default(0); // Click Through Rate
            $table->bigInteger('likes')->default(0);
            $table->bigInteger('comments')->default(0);
            $table->bigInteger('shares')->default(0);
            $table->bigInteger('saves')->default(0);
            
            // مؤشرات التحويل
            $table->bigInteger('conversions')->default(0);
            $table->decimal('conversion_rate', 8, 4)->default(0);
            $table->bigInteger('leads')->default(0);
            $table->bigInteger('purchases')->default(0);
            $table->decimal('revenue', 12, 2)->default(0);
            
            // مؤشرات التكلفة
            $table->decimal('spend', 12, 2)->default(0);
            $table->decimal('cpm', 10, 4)->default(0); // Cost per 1000 impressions
            $table->decimal('cpc', 10, 4)->default(0); // Cost per click
            $table->decimal('cpl', 10, 4)->default(0); // Cost per lead
            $table->decimal('cpa', 10, 4)->default(0); // Cost per acquisition
            $table->decimal('roas', 10, 4)->default(0); // Return on Ad Spend
            
            // مؤشرات الفيديو
            $table->bigInteger('video_views')->default(0);
            $table->bigInteger('video_views_25')->default(0);
            $table->bigInteger('video_views_50')->default(0);
            $table->bigInteger('video_views_75')->default(0);
            $table->bigInteger('video_views_100')->default(0);
            
            $table->timestamps();
            $table->unique(['campaign_id', 'date']);
        });

        // campaign_analyses - تحليلات AI
        Schema::create('campaign_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('analysis_type'); // performance, comparison, recommendation, full
            
            $table->json('plan_comparison')->nullable(); // مقارنة مع الخطة
            $table->json('strengths')->nullable();
            $table->json('weaknesses')->nullable();
            $table->json('recommendations')->nullable();
            $table->json('benchmarks')->nullable();
            $table->decimal('overall_score', 5, 2)->nullable();
            
            $table->text('ai_summary')->nullable();
            $table->json('ai_insights')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_analyses');
        Schema::dropIfExists('campaign_metrics');
        Schema::dropIfExists('campaigns');
    }
};
