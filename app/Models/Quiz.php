<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'course_id',
        'title',
        'description',
        'quiz_type',
        'passing_score',
        'time_limit_minutes',
        'show_correct_answers',
        'allow_retake',
        'is_active'
    ];

    protected $casts = [
        'show_correct_answers' => 'boolean',
        'allow_retake' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the lesson that owns the quiz.
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get the course that owns the quiz.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the questions for the quiz.
     */
    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order_number');
    }

    /**
     * Get the attempts for the quiz.
     */
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Get user's attempts for this quiz.
     */
    public function getUserAttempts($userId)
    {
        return $this->attempts()->where('user_id', $userId)->get();
    }

    /**
     * Get user's best score for this quiz.
     */
    public function getUserBestScore($userId): ?int
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->max('percentage');
    }

    /**
     * Check if user has passed this quiz.
     */
    public function isPassedBy($userId): bool
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->where('passed', true)
            ->exists();
    }

    /**
     * Calculate total points for the quiz.
     */
    public function getTotalPoints(): int
    {
        return $this->questions()->sum('points');
    }
}
