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
        // جدول الدورات (Courses)
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->string('thumbnail_url')->nullable();
            $table->integer('order_number')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('total_lessons')->default(0);
            $table->integer('estimated_hours')->default(0);
            $table->timestamps();
        });

        // جدول الدروس (Lessons)
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('lesson_number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->longText('content'); // المحتوى النصي للدرس
            $table->json('learning_objectives')->nullable(); // أهداف التعلم
            $table->json('key_concepts')->nullable(); // المفاهيم الأساسية
            $table->string('video_url')->nullable();
            $table->integer('estimated_minutes')->default(30);
            $table->integer('order_number')->default(0);
            $table->boolean('has_quiz')->default(false);
            $table->boolean('has_tool')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['course_id', 'lesson_number']);
        });

        // جدول الأدوات التفاعلية (Tools)
        Schema::create('lesson_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->string('tool_type'); // calculator, template, analyzer, planner, etc.
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('tool_config'); // إعدادات الأداة (fields, calculations, etc.)
            $table->json('example_data')->nullable(); // بيانات مثال
            $table->string('component_name'); // اسم Vue component
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // جدول الاختبارات (Quizzes)
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('quiz_type', ['lesson', 'final'])->default('lesson');
            $table->integer('passing_score')->default(70); // النسبة المئوية للنجاح
            $table->integer('time_limit_minutes')->nullable();
            $table->boolean('show_correct_answers')->default(true);
            $table->boolean('allow_retake')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['lesson_id', 'quiz_type']);
        });

        // جدول أسئلة الاختبار (Quiz Questions)
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->enum('question_type', ['multiple_choice', 'true_false', 'multiple_select'])->default('multiple_choice');
            $table->json('options'); // خيارات الإجابة
            $table->json('correct_answers'); // الإجابات الصحيحة
            $table->text('explanation')->nullable(); // شرح الإجابة
            $table->integer('points')->default(1);
            $table->integer('order_number')->default(0);
            $table->timestamps();
        });

        // جدول تقدم المستخدم في الدروس (User Progress)
        Schema::create('user_lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->boolean('is_completed')->default(false);
            $table->integer('progress_percentage')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id']);
        });

        // جدول محاولات الاختبار (Quiz Attempts)
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->integer('score')->default(0);
            $table->integer('max_score')->default(0);
            $table->integer('percentage')->default(0);
            $table->boolean('passed')->default(false);
            $table->json('answers'); // إجابات المستخدم
            $table->integer('time_taken_seconds')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'quiz_id']);
        });

        // جدول استخدام الأدوات (Tool Usage)
        Schema::create('tool_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_tool_id')->constrained()->onDelete('cascade');
            $table->json('input_data')->nullable();
            $table->json('output_data')->nullable();
            $table->boolean('saved_result')->default(false);
            $table->timestamp('used_at')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'lesson_tool_id']);
        });

        // جدول الشهادات (Certificates)
        Schema::create('course_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->integer('final_score')->default(0);
            $table->date('issue_date');
            $table->string('certificate_url')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_certificates');
        Schema::dropIfExists('tool_usage');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('user_lesson_progress');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('lesson_tools');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('courses');
    }
};
