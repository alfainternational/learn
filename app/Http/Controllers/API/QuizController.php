<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function show($id)
    {
        $quiz = Quiz::with(['questions' => function ($query) {
            $query->select('id', 'quiz_id', 'question', 'question_type', 'options', 'order_number')
                ->orderBy('order_number');
        }])->findOrFail($id);

        $quiz->questions->each(function ($question) {
            unset($question->correct_answer);
        });

        return response()->json(['success' => true, 'data' => $quiz]);
    }

    public function attempt(Request $request, $id)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:quiz_questions,id',
            'answers.*.answer' => 'required'
        ]);

        $quiz = Quiz::with('questions')->findOrFail($id);
        $totalQuestions = $quiz->questions->count();
        $correctAnswers = 0;

        foreach ($request->answers as $answer) {
            $question = $quiz->questions->find($answer['question_id']);
            if ($question && $question->correct_answer == $answer['answer']) {
                $correctAnswers++;
            }
        }

        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
        $passed = $score >= $quiz->passing_score;

        $attempt = QuizAttempt::create([
            'user_id' => auth()->id(),
            'quiz_id' => $id,
            'score' => $score,
            'passed' => $passed,
            'answers' => $request->answers,
            'started_at' => now(),
            'completed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => $passed ? 'تهانينا! لقد اجتزت الاختبار' : 'لم تجتز الاختبار',
            'data' => ['attempt_id' => $attempt->id, 'score' => $score, 'passed' => $passed]
        ]);
    }

    public function results($attemptId)
    {
        $attempt = QuizAttempt::with(['quiz.questions'])
            ->where('user_id', auth()->id())
            ->findOrFail($attemptId);

        $detailedResults = collect($attempt->answers)->map(function ($answer) use ($attempt) {
            $question = $attempt->quiz->questions->find($answer['question_id']);
            return [
                'question_id' => $answer['question_id'],
                'question' => $question->question ?? '',
                'your_answer' => $answer['answer'],
                'correct_answer' => $question->correct_answer ?? '',
                'is_correct' => $question && $question->correct_answer == $answer['answer']
            ];
        });

        return response()->json([
            'success' => true,
            'data' => ['attempt' => $attempt, 'detailed_results' => $detailedResults]
        ]);
    }
}
