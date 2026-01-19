<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'thumbnail_url',
        'order_number',
        'is_active',
        'total_lessons',
        'estimated_hours'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }
        });
    }

    /**
     * Get the lessons for the course.
     */
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order_number');
    }

    /**
     * Get the quizzes for the course.
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Get the final quiz for the course.
     */
    public function finalQuiz()
    {
        return $this->hasOne(Quiz::class)->where('quiz_type', 'final');
    }

    /**
     * Get certificates issued for this course.
     */
    public function certificates()
    {
        return $this->hasMany(CourseCertificate::class);
    }

    /**
     * Get user progress for this course.
     */
    public function userProgress($userId)
    {
        return $this->lessons()
            ->with(['progress' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->get();
    }

    /**
     * Calculate course completion percentage for a user.
     */
    public function getCompletionPercentage($userId): int
    {
        $totalLessons = $this->lessons()->count();
        if ($totalLessons === 0) {
            return 0;
        }

        $completedLessons = UserLessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $this->lessons()->pluck('id'))
            ->where('is_completed', true)
            ->count();

        return round(($completedLessons / $totalLessons) * 100);
    }
}
