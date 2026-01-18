<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PlanTemplate;
use App\Models\MarketingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TemplateController extends Controller
{
    /**
     * قائمة القوالب المتاحة
     */
    public function index(Request $request)
    {
        $query = PlanTemplate::active();

        // تصفية حسب الصناعة
        if ($request->has('industry')) {
            $query->byIndustry($request->industry);
        }

        // تصفية حسب النوع (مجاني/مدفوع)
        if ($request->has('type')) {
            if ($request->type === 'free') {
                $query->free();
            } elseif ($request->type === 'premium') {
                $query->premium();
            }
        }

        // القوالب المميزة
        if ($request->boolean('featured')) {
            $query->featured();
        }

        $templates = $query->latest()->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }

    /**
     * عرض قالب محدد
     */
    public function show(PlanTemplate $template)
    {
        if (!$template->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'القالب غير متاح',
            ], 404);
        }

        $template->load('creator:id,name');

        return response()->json([
            'success' => true,
            'data' => ['template' => $template],
        ]);
    }

    /**
     * إنشاء خطة من قالب
     */
    public function createFromTemplate(Request $request, PlanTemplate $template)
    {
        $user = $request->user();

        // التحقق من إمكانية إنشاء خطة جديدة
        if (!$user->canCreatePlan()) {
            return response()->json([
                'success' => false,
                'message' => 'لقد وصلت للحد الأقصى من الخطط',
            ], 403);
        }

        // التحقق من القالب Premium
        if ($template->is_premium && !$user->hasActiveSubscription()) {
            return response()->json([
                'success' => false,
                'message' => 'هذا قالب مدفوع. يرجى الترقية للاشتراك المدفوع.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // إنشاء خطة جديدة من القالب
        $plan = $user->marketingPlans()->create([
            'title' => $request->business_name . ' - ' . $template->name,
            'business_name' => $request->business_name,
            'industry' => $template->industry,
            'year' => now()->year,
            'status' => 'draft',
        ]);

        // نسخ أقسام القالب
        $templateData = $template->template_data;
        foreach ($templateData['sections'] ?? [] as $sectionType => $sectionData) {
            $plan->sections()->create([
                'section_type' => $sectionType,
                'section_data' => $sectionData,
                'is_completed' => false,
            ]);
        }

        // تحديث إحصائيات القالب
        $template->incrementUsage();

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الخطة من القالب بنجاح',
            'data' => ['plan' => $plan->load('sections')],
        ], 201);
    }

    /**
     * حفظ خطة كقالب
     */
    public function saveAsTemplate(Request $request)
    {
        $user = $request->user();

        // يجب أن يكون لديه اشتراك مدفوع
        if (!$user->hasActiveSubscription()) {
            return response()->json([
                'success' => false,
                'message' => 'هذه الميزة متاحة للمشتركين المدفوعين فقط',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:marketing_plans,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $plan = MarketingPlan::findOrFail($request->plan_id);

        // التحقق من الملكية
        if ($plan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحفظ هذه الخطة كقالب',
            ], 403);
        }

        // بناء بيانات القالب
        $templateData = [
            'sections' => [],
        ];

        foreach ($plan->sections as $section) {
            $templateData['sections'][$section->section_type] = $section->section_data;
        }

        // إنشاء القالب
        $template = PlanTemplate::create([
            'name' => $request->name,
            'description' => $request->description,
            'industry' => $plan->industry,
            'template_data' => $templateData,
            'created_by' => $user->id,
            'is_premium' => false,
            'is_active' => $request->boolean('is_public'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ القالب بنجاح',
            'data' => ['template' => $template],
        ], 201);
    }

    /**
     * قوالبي (القوالب التي أنشأها المستخدم)
     */
    public function myTemplates(Request $request)
    {
        $templates = PlanTemplate::where('created_by', $request->user()->id)
            ->latest()
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }

    /**
     * تحديث قالب (للمنشئ فقط)
     */
    public function update(Request $request, PlanTemplate $template)
    {
        if ($template->created_by !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتعديل هذا القالب',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:500',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $template->update($request->only(['name', 'description', 'is_active']));

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث القالب بنجاح',
            'data' => ['template' => $template],
        ]);
    }

    /**
     * حذف قالب
     */
    public function destroy(Request $request, PlanTemplate $template)
    {
        if ($template->created_by !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف هذا القالب',
            ], 403);
        }

        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف القالب بنجاح',
        ]);
    }
}
