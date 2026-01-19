<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserLessonProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_courses()
    {
        $user = User::factory()->create();
        Course::factory()->count(3)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/courses');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_view_course()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/courses/{$course->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $course->id]);
    }

    public function test_can_get_course_progress()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);
        
        UserLessonProgress::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'completed' => true,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/courses/{$course->id}/progress");

        $response->assertStatus(200);
    }
}
