<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserLessonProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_lesson()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $response = $this->actingAs($user)
            ->getJson("/api/lessons/{$lesson->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $lesson->id]);
    }

    public function test_can_complete_lesson()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $response = $this->actingAs($user)
            ->postJson("/api/lessons/{$lesson->id}/complete");

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('user_lesson_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'completed' => true,
        ]);
    }

    public function test_can_update_progress()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $response = $this->actingAs($user)
            ->putJson("/api/lessons/{$lesson->id}/progress", [
                'progress_percentage' => 50,
            ]);

        $response->assertStatus(200);
    }
}
