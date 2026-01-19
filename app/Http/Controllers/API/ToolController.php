<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LessonTool;
use App\Models\ToolUsage;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    /**
     * تفاصيل الأداة
     */
    public function show($id)
    {
        $tool = LessonTool::with('lesson')->findOrFail($id);

        if (auth()->check()) {
            $tool->usage_count = ToolUsage::where('user_id', auth()->id())
                ->where('tool_id', $id)
                ->count();
        }

        return response()->json([
            'success' => true,
            'data' => $tool
        ]);
    }

    /**
     * تسجيل استخدام الأداة
     */
    public function use(Request $request, $id)
    {
        $tool = LessonTool::findOrFail($id);

        $usage = ToolUsage::create([
            'user_id' => auth()->id(),
            'tool_id' => $id,
            'input_data' => $request->input('input_data'),
            'used_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل استخدام الأداة',
            'data' => $usage
        ]);
    }

    /**
     * حفظ نتيجة الأداة
     */
    public function saveResult(Request $request, $id)
    {
        $request->validate([
            'usage_id' => 'required|exists:tool_usages,id',
            'result_data' => 'required'
        ]);

        $usage = ToolUsage::where('id', $request->usage_id)
            ->where('user_id', auth()->id())
            ->where('tool_id', $id)
            ->firstOrFail();

        $usage->update([
            'result_data' => $request->result_data
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ النتيجة',
            'data' => $usage
        ]);
    }
}
