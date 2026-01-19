<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_quiz()
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $quiz = Quiz::factory()->create(['lesson_id' => $lesson->id]);

        $response = $this->actingAs($user)
            ->getJson("/api/quizzes/{$quiz->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $quiz->id]);
    }

    public function test_can_submit_attempt()
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $quiz = Quiz::factory()->create(['lesson_id' => $lesson->id]);
        $question = QuizQuestion::factory()->create([
            'quiz_id' => $quiz->id,
            'correct_answer' => 'A',
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/quizzes/{$quiz->id}/attempt", [
                'answers' => [
                    ['question_id' => $question->id, 'answer' => 'A'],
                ],
            ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('quiz_attempts', [
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
        ]);
    }

    public function test_can_view_results()
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $quiz = Quiz::factory()->create(['lesson_id' => $lesson->id]);
        $attempt = QuizAttempt::factory()->create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => 80,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/quiz-attempts/{$attempt->id}/results");

        $response->assertStatus(200);
    }
}
