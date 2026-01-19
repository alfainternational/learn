<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PlanTemplate;
use App\Models\MarketingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemplateController extends Controller
{
    public function index(Request $request)
    {
        $templates = PlanTemplate::where('is_active', true)
            ->when($request->category, function ($query, $category) {
                return $query->where('category', $category);
            })
            ->latest()
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }

    public function show(PlanTemplate $template)
    {
        if (!$template->is_active) {
            abort(404);
        }

        return response()->json([
            'success' => true,
            'data' => $template->load('sections'),
        ]);
    }

    public function useTemplate(Request $request, PlanTemplate $template)
    {
        $user = $request->user();

        if (!$user->canCreatePlan()) {
            return response()->json([
                'success' => false,
                'message' => 'لقد وصلت للحد الأقصى من الخطط المسموح بها.',
            ], 403);
        }

        $plan = DB::transaction(function () use ($user, $template) {
            $plan = $user->marketingPlans()->create([
                'title' => $template->name . ' - ' . $user->name,
                'business_name' => 'مشروع جديد',
                'industry' => $template->category,
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
            'message' => 'تم إنشاء الخطة من القالب بنجاح',
            'data' => $plan->load('sections'),
        ], 201);
    }
}
