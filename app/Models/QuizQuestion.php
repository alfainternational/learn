<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'question_type',
        'options',
        'correct_answers',
        'explanation',
        'points',
        'order_number'
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answers' => 'array',
    ];

    /**
     * Get the quiz that owns the question.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Check if the given answer is correct.
     */
    public function isCorrectAnswer($userAnswer): bool
    {
        if ($this->question_type === 'multiple_select') {
            sort($userAnswer);
            $correct = $this->correct_answers;
            sort($correct);
            return $userAnswer === $correct;
        }

        return in_array($userAnswer, $this->correct_answers);
    }
}
