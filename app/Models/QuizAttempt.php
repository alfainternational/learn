<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'max_score',
        'percentage',
        'passed',
        'answers',
        'time_taken_seconds',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'passed' => 'boolean',
        'answers' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the attempt.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the quiz that owns the attempt.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Calculate score for this attempt.
     */
    public function calculateScore(): void
    {
        $quiz = $this->quiz()->with('questions')->first();
        $score = 0;
        $maxScore = 0;

        foreach ($quiz->questions as $question) {
            $maxScore += $question->points;

            $userAnswer = $this->answers[$question->id] ?? null;

            if ($userAnswer && $question->isCorrectAnswer($userAnswer)) {
                $score += $question->points;
            }
        }

        $this->score = $score;
        $this->max_score = $maxScore;
        $this->percentage = $maxScore > 0 ? round(($score / $maxScore) * 100) : 0;
        $this->passed = $this->percentage >= $quiz->passing_score;
        $this->save();
    }
}
