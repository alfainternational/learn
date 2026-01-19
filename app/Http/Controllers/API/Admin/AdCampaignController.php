<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdCampaign;
use Illuminate\Http\Request;

class AdCampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = AdCampaign::with('advertiser');

        if ($request->has('status')) {
             if ($request->status !== 'all') {
                 $query->where('status', $request->status);
             }
        }

        $campaigns = $query->latest()->paginate(20);
        return response()->json(['success' => true, 'data' => $campaigns]);
    }

    public function pending()
    {
        $campaigns = AdCampaign::with('advertiser')->where('status', 'pending_review')->latest()->get();
        return response()->json(['success' => true, 'data' => $campaigns]);
    }

    public function approve(AdCampaign $campaign)
    {
        $campaign->update([
            'status' => 'active', 
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now()
        ]);
        
        // Notify advertiser logic here later
        
        return response()->json(['success' => true, 'message' => 'Campaign approved successfully', 'data' => $campaign]);
    }

    public function reject(Request $request, AdCampaign $campaign)
    {
        $request->validate(['reason' => 'required|string']);

        $campaign->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Campaign rejected', 'data' => $campaign]);
    }
}
