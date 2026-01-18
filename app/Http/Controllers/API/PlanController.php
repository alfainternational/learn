<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MarketingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
    /**
     * قائمة الخطط للمستخدم الحالي
     */
    public function index(Request $request)
    {
        $plans = $request->user()
            ->marketingPlans()
            ->with('sections')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $plans,
        ]);
    }

    /**
     * إنشاء خطة جديدة
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->canCreatePlan()) {
            return response()->json([
                'success' => false,
                'message' => 'لقد وصلت للحد الأقصى من الخطط. قم بالترقية للاشتراك المدفوع.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'business_name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:2024|max:2030',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $plan = $user->marketingPlans()->create([
            'title' => $request->title ?? "خطة {$request->business_name}",
            'business_name' => $request->business_name,
            'industry' => $request->industry,
            'year' => $request->year ?? now()->year,
            'status' => 'draft',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الخطة بنجاح',
            'data' => ['plan' => $plan],
        ], 201);
    }

    /**
     * عرض خطة محددة
     */
    public function show(Request $request, MarketingPlan $plan)
    {
        if ($plan->user_id !== $request->user()->id && !$plan->is_public) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بعرض هذه الخطة',
            ], 403);
        }

        $plan->load('sections', 'user:id,name,avatar_url');

        return response()->json([
            'success' => true,
            'data' => ['plan' => $plan],
        ]);
    }

    /**
     * تحديث خطة
     */
    public function update(Request $request, MarketingPlan $plan)
    {
        if ($plan->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتعديل هذه الخطة',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:draft,in_progress,completed,archived',
            'business_name' => 'sometimes|string|max:255',
            'industry' => 'sometimes|string|max:100',
            'marketing_goal' => 'sometimes|string',
            'budget_monthly' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $plan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الخطة بنجاح',
            'data' => ['plan' => $plan],
        ]);
    }

    /**
     * حذف خطة
     */
    public function destroy(Request $request, MarketingPlan $plan)
    {
        if ($plan->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف هذه الخطة',
            ], 403);
        }

        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الخطة بنجاح',
        ]);
    }

    /**
     * الحصول على أقسام خطة محددة
     */
    public function getSections(MarketingPlan $plan)
    {
        $sections = $plan->sections;

        return response()->json([
            'success' => true,
            'data' => ['sections' => $sections],
        ]);
    }

    /**
     * تحديث قسم محدد
     */
    public function updateSection(Request $request, MarketingPlan $plan, string $sectionType)
    {
        if ($plan->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتعديل هذا القسم',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'section_data' => 'required|array',
            'is_completed' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $section = $plan->updateSectionData(
            $sectionType,
            $request->section_data,
            $request->is_completed ?? false
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث القسم بنجاح',
            'data' => ['section' => $section],
        ]);
    }

    /**
     * نسخ خطة (Duplicate)
     */
    public function duplicate(Request $request, MarketingPlan $plan)
    {
        if ($plan->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بنسخ هذه الخطة',
            ], 403);
        }

        if (!$request->user()->canCreatePlan()) {
            return response()->json([
                'success' => false,
                'message' => 'لقد وصلت للحد الأقصى من الخطط',
            ], 403);
        }

        $newPlan = $plan->replicate();
        $newPlan->title = $plan->title . ' (نسخة)';
        $newPlan->status = 'draft';
        $newPlan->is_public = false;
        $newPlan->share_token = null;
        $newPlan->view_count = 0;
        $newPlan->save();

        foreach ($plan->sections as $section) {
            $newSection = $section->replicate();
            $newSection->plan_id = $newPlan->id;
            $newSection->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'تم نسخ الخطة بنجاح',
            'data' => ['plan' => $newPlan],
        ]);
    }

    /**
     * أرشفة خطة
     */
    public function archive(Request $request, MarketingPlan $plan)
    {
        if ($plan->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح',
            ], 403);
        }

        $plan->update(['status' => 'archived']);

        return response()->json([
            'success' => true,
            'message' => 'تم أرشفة الخطة',
        ]);
    }

    /**
     * توليد رابط مشاركة
     */
    public function generateShareLink(Request $request, MarketingPlan $plan)
    {
        if ($plan->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح',
            ], 403);
        }

        $token = $plan->generateShareToken();
        $plan->update(['is_public' => true]);

        $shareUrl = url("/api/v1/plans/shared/{$token}");

        return response()->json([
            'success' => true,
            'message' => 'تم توليد رابط المشاركة',
            'data' => [
                'share_token' => $token,
                'share_url' => $shareUrl,
            ],
        ]);
    }

    /**
     * إلغاء رابط المشاركة
     */
    public function revokeShareLink(Request $request, MarketingPlan $plan)
    {
        if ($plan->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح',
            ], 403);
        }

        $plan->revokeShareToken();

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء رابط المشاركة',
        ]);
    }

    /**
     * عرض خطة مشاركة (Public)
     */
    public function showShared(string $token)
    {
        $plan = MarketingPlan::where('share_token', $token)
            ->where('is_public', true)
            ->with('sections', 'user:id,name,avatar_url')
            ->firstOrFail();

        $plan->incrementViewCount();

        return response()->json([
            'success' => true,
            'data' => ['plan' => $plan],
        ]);
    }

    /**
     * تصدير خطة إلى PDF
     */
    public function exportPdf(Request $request, MarketingPlan $plan)
    {
        if ($plan->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح',
            ], 403);
        }

        // سيتم تنفيذها في Phase 5 (Export Service)
        return response()->json([
            'success' => true,
            'message' => 'سيتم تنفيذ التصدير قريباً',
        ]);
    }

    /**
     * تصدير خطة إلى Word
     */
    public function exportDocx(Request $request, MarketingPlan $plan)
    {
        if ($plan->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'سيتم تنفيذ التصدير قريباً',
        ]);
    }

    /**
     * تصدير خطة إلى Excel
     */
    public function exportExcel(Request $request, MarketingPlan $plan)
    {
        if ($plan->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'سيتم تنفيذ التصدير قريباً',
        ]);
    }
}
