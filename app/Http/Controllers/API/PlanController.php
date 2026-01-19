<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\CreatePlanRequest;
use App\Models\MarketingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $plans = $request->user()->marketingPlans()
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $plans,
        ]);
    }

    public function store(CreatePlanRequest $request)
    {
        $user = $request->user();

        if (!$user->canCreatePlan()) {
            return response()->json([
                'success' => false,
                'message' => 'لقد وصلت للحد الأقصى من الخطط المسموح بها في باقتك الحالية.',
            ], 403);
        }

        $plan = DB::transaction(function () use ($request, $user) {
            $plan = $user->marketingPlans()->create([
                'title' => $request->business_name . ' - خطة تسويقية',
                'business_name' => $request->business_name,
                'industry' => $request->industry,
                'status' => 'draft',
                'completion_percentage' => 0,
            ]);

            // Initialize default sections
            $sections = [
                'diagnosis', 'target_audience', 'core_message', 
                'offer_stack', 'channels', 'content_plan'
            ];
            
            foreach ($sections as $type) {
                $plan->sections()->create(['section_type' => $type]);
            }

            return $plan;
        });

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الخطة بنجاح',
            'data' => $plan->load('sections'),
        ], 201);
    }

    public function show(MarketingPlan $plan)
    {
        $this->authorize('view', $plan);

        return response()->json([
            'success' => true,
            'data' => $plan->load('sections'),
        ]);
    }

    public function update(Request $request, MarketingPlan $plan)
    {
        $this->authorize('update', $plan);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'business_name' => 'string|max:255',
        ]);

        $plan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الخطة بنجاح',
            'data' => $plan,
        ]);
    }

    public function destroy(MarketingPlan $plan)
    {
        $this->authorize('delete', $plan);

        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الخطة بنجاح',
        ]);
    }

    public function updateSection(Request $request, MarketingPlan $plan, $sectionType)
    {
        $this->authorize('update', $plan);

        $section = $plan->sections()->where('section_type', $sectionType)->firstOrFail();

        $section->update([
            'section_data' => $request->input('section_data'),
            'is_completed' => $request->boolean('is_completed'),
        ]);

        $plan->updateCompletionPercentage();

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ القسم بنجاح',
            'data' => $section,
        ]);
    }

    public function duplicate(Request $request, MarketingPlan $plan)
    {
        $this->authorize('view', $plan); // User can view to duplicate
        
        $newPlan = $plan->replicate(['uuid', 'created_at', 'updated_at']);
        $newPlan->title = $plan->title . ' (نسخة)';
        $newPlan->status = 'draft';
        $newPlan->save();

        foreach ($plan->sections as $section) {
            $newSection = $section->replicate(['plan_id', 'created_at', 'updated_at']);
            $newSection->plan_id = $newPlan->id;
            $newSection->save();
        }

        return response()->json(['success' => true, 'data' => $newPlan], 201);
    }

    public function archive(MarketingPlan $plan)
    {
        $this->authorize('update', $plan);
        $plan->update(['status' => 'archived']);
        return response()->json(['success' => true, 'message' => 'تم أرشفة الخطة']);
    }

    public function generateShareLink(MarketingPlan $plan)
    {
        $this->authorize('update', $plan);
        // Simplified Logic: Assuming a share_token field or similar exists or we create one
        // For now, let's just mock it or assume simple public toggle
        $plan->update(['is_public' => true]);
        
        return response()->json(['success' => true, 'data' => ['url' => url("/plans/shared/{$plan->id}")]]);
    }
    
    public function revokeShareLink(MarketingPlan $plan)
    {
        $this->authorize('update', $plan);
        $plan->update(['is_public' => false]);
        return response()->json(['success' => true, 'message' => 'تم تعطيل الرابط']);
    }

    public function showShared($token)
    {
        // Assuming token is ID for now for simplicity, ideally it's a uuid or hash
        $plan = MarketingPlan::findOrFail($token); 
        if (!$plan->is_public) {
             abort(404);
        }
        return response()->json(['success' => true, 'data' => $plan->load('sections')]);
    }

    protected $exportService;

    public function __construct(\App\Services\Export\ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function exportPdf(MarketingPlan $plan) 
    { 
        $this->authorize('view', $plan);
        return $this->exportService->exportPdf($plan);
    }
    
    public function exportExcel(MarketingPlan $plan) 
    { 
        $this->authorize('view', $plan);
        return response()->json($this->exportService->exportExcel($plan));
    }
    
    public function exportDocx(MarketingPlan $plan) { return response()->json(['message' => 'Docx export pending']); }
}
