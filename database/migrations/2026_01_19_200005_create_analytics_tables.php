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
        // analytics_snapshots - لقطات التحليلات
        Schema::create('analytics_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('period'); // daily, weekly, monthly, quarterly, yearly
            $table->date('start_date');
            $table->date('end_date');
            
            // إجماليات
            $table->decimal('total_spend', 14, 2)->default(0);
            $table->decimal('total_revenue', 14, 2)->default(0);
            $table->decimal('total_roas', 10, 4)->default(0);
            $table->bigInteger('total_impressions')->default(0);
            $table->bigInteger('total_clicks')->default(0);
            $table->bigInteger('total_conversions')->default(0);
            
            // تفاصيل حسب القناة
            $table->json('by_platform')->nullable();
            $table->json('by_campaign_type')->nullable();
            $table->json('top_campaigns')->nullable();
            
            // المقارنة مع الفترة السابقة
            $table->json('comparison')->nullable();
            
            $table->timestamps();
        });

        // reports - التقارير المحفوظة
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('name');
            $table->string('type'); // campaign, period, comparison, custom
            $table->json('filters')->nullable();
            $table->json('data');
            
            $table->string('format')->default('json'); // json, pdf, excel
            $table->string('file_path')->nullable();
            
            $table->boolean('is_scheduled')->default(false);
            $table->string('schedule_frequency')->nullable(); // weekly, monthly
            $table->json('schedule_recipients')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('analytics_snapshots');
    }
};
