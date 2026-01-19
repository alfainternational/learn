<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserLessonProgress;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * قائمة الدورات
     */
    public function index()
    {
        $courses = Course::where('is_active', true)
            ->with('lessons')
            ->orderBy('order_number')
            ->get()
            ->map(function ($course) {
                $course->completion_percentage = auth()->check() 
                    ? $course->getCompletionPercentage(auth()->id()) 
                    : 0;
                return $course;
            });

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }

    /**
     * تفاصيل دورة مع lessons
     */
    public function show($id)
    {
        $course = Course::with(['lessons' => function ($query) {
            $query->orderBy('order_number');
        }])->findOrFail($id);

        if (auth()->check()) {
            $course->completion_percentage = $course->getCompletionPercentage(auth()->id());
            
            $lessonIds = $course->lessons->pluck('id');
            $progress = UserLessonProgress::where('user_id', auth()->id())
                ->whereIn('lesson_id', $lessonIds)
                ->pluck('is_completed', 'lesson_id');

            $course->lessons->each(function ($lesson) use ($progress) {
                $lesson->is_completed = $progress[$lesson->id] ?? false;
            });
        }

        return response()->json([
            'success' => true,
            'data' => $course
        ]);
    }

    /**
     * تقدم المستخدم في الدورة
     */
    public function progress($id)
    {
        $course = Course::with('lessons')->findOrFail($id);
        $userId = auth()->id();

        $lessonIds = $course->lessons->pluck('id');
        $progressRecords = UserLessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $lessonIds)
            ->get()
            ->keyBy('lesson_id');

        $lessonsProgress = $course->lessons->map(function ($lesson) use ($progressRecords) {
            $progress = $progressRecords[$lesson->id] ?? null;
            return [
                'lesson_id' => $lesson->id,
                'title' => $lesson->title,
                'is_completed' => $progress ? $progress->is_completed : false,
                'progress_percentage' => $progress ? $progress->progress_percentage : 0,
                'completed_at' => $progress ? $progress->completed_at : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'total_lessons' => $course->lessons->count(),
                'completed_lessons' => $progressRecords->where('is_completed', true)->count(),
                'overall_percentage' => $course->getCompletionPercentage($userId),
                'lessons' => $lessonsProgress
            ]
        ]);
    }
}
