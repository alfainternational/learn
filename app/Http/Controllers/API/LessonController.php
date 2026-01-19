<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\UserLessonProgress;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    /**
     * تفاصيل الدرس مع tools و quiz
     */
    public function show($id)
    {
        $lesson = Lesson::with(['tools', 'quiz.questions'])->findOrFail($id);

        if (auth()->check()) {
            $progress = UserLessonProgress::where('user_id', auth()->id())
                ->where('lesson_id', $id)
                ->first();
            
            $lesson->user_progress = $progress ? [
                'is_completed' => $progress->is_completed,
                'progress_percentage' => $progress->progress_percentage,
                'completed_at' => $progress->completed_at
            ] : null;
        }

        return response()->json([
            'success' => true,
            'data' => $lesson
        ]);
    }

    /**
     * تسجيل إكمال الدرس
     */
    public function complete($id)
    {
        $lesson = Lesson::findOrFail($id);

        $progress = UserLessonProgress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'lesson_id' => $id
            ],
            [
                'is_completed' => true,
                'progress_percentage' => 100,
                'completed_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إكمال الدرس بنجاح',
            'data' => $progress
        ]);
    }

    /**
     * تحديث نسبة الإنجاز
     */
    public function updateProgress(Request $request, $id)
    {
        $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100'
        ]);

        $lesson = Lesson::findOrFail($id);
        $percentage = $request->progress_percentage;

        $progress = UserLessonProgress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'lesson_id' => $id
            ],
            [
                'progress_percentage' => $percentage,
                'is_completed' => $percentage >= 100,
                'completed_at' => $percentage >= 100 ? now() : null
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث التقدم',
            'data' => $progress
        ]);
    }
}
