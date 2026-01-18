<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('industry', 100)->nullable();
            $table->json('template_data');
            $table->text('thumbnail_url')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->decimal('price', 8, 2)->default(0);
            $table->integer('usage_count')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('industry');
            $table->index(['is_premium', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_templates');
    }
};
