<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\LessonTool;
use App\Models\ToolUsage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToolTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_tool()
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $tool = LessonTool::factory()->create(['lesson_id' => $lesson->id]);

        $response = $this->actingAs($user)
            ->getJson("/api/tools/{$tool->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $tool->id]);
    }

    public function test_can_use_tool()
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $tool = LessonTool::factory()->create(['lesson_id' => $lesson->id]);

        $response = $this->actingAs($user)
            ->postJson("/api/tools/{$tool->id}/use", [
                'input_data' => ['test' => 'data'],
            ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('tool_usages', [
            'user_id' => $user->id,
            'lesson_tool_id' => $tool->id,
        ]);
    }

    public function test_can_save_result()
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $tool = LessonTool::factory()->create(['lesson_id' => $lesson->id]);
        $usage = ToolUsage::factory()->create([
            'user_id' => $user->id,
            'lesson_tool_id' => $tool->id,
        ]);

        $response = $this->actingAs($user)
            ->putJson("/api/tool-usages/{$usage->id}/save", [
                'result_data' => ['output' => 'result'],
            ]);

        $response->assertStatus(200);
    }
}
