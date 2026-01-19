<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'lesson_number',
        'title',
        'description',
        'slug',
        'content',
        'learning_objectives',
        'key_concepts',
        'video_url',
        'estimated_minutes',
        'order_number',
        'has_quiz',
        'has_tool',
        'is_active'
    ];

    protected $casts = [
        'learning_objectives' => 'array',
        'key_concepts' => 'array',
        'has_quiz' => 'boolean',
        'has_tool' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lesson) {
            if (empty($lesson->slug)) {
                $lesson->slug = Str::slug($lesson->title);
            }
        });
    }

    /**
     * Get the course that owns the lesson.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the quiz for the lesson.
     */
    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    /**
     * Get the tools for the lesson.
     */
    public function tools()
    {
        return $this->hasMany(LessonTool::class);
    }

    /**
     * Get user progress for this lesson.
     */
    public function progress()
    {
        return $this->hasMany(UserLessonProgress::class);
    }

    /**
     * Get user's progress for this lesson.
     */
    public function getUserProgress($userId)
    {
        return $this->progress()->where('user_id', $userId)->first();
    }

    /**
     * Check if user has completed this lesson.
     */
    public function isCompletedBy($userId): bool
    {
        return $this->progress()
            ->where('user_id', $userId)
            ->where('is_completed', true)
            ->exists();
    }

    /**
     * Mark lesson as completed by user.
     */
    public function markAsCompleted($userId): void
    {
        UserLessonProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'lesson_id' => $this->id
            ],
            [
                'is_completed' => true,
                'progress_percentage' => 100,
                'completed_at' => now()
            ]
        );
    }

    /**
     * Update user progress percentage.
     */
    public function updateProgress($userId, int $percentage): void
    {
        UserLessonProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'lesson_id' => $this->id
            ],
            [
                'progress_percentage' => $percentage,
                'started_at' => now()
            ]
        );
    }
}
