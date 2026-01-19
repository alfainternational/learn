<?php

namespace App\Http\Controllers\API\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\AdCampaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $campaigns = $request->user()->adCampaigns()->latest()->paginate(10);
        return response()->json(['success' => true, 'data' => $campaigns]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'budget' => 'required|numeric',
        ]);

        $campaign = $request->user()->adCampaigns()->create($request->all());

        return response()->json(['success' => true, 'data' => $campaign, 'message' => 'Campaign created successfully'], 201);
    }

    public function show(AdCampaign $campaign)
    {
        $this->authorize('view', $campaign);
        return response()->json(['success' => true, 'data' => $campaign]);
    }

    public function update(Request $request, AdCampaign $campaign)
    {
        $this->authorize('update', $campaign);
        $campaign->update($request->all());
        return response()->json(['success' => true, 'data' => $campaign]);
    }

    public function destroy(AdCampaign $campaign)
    {
        $this->authorize('delete', $campaign);
        $campaign->delete();
        return response()->json(['success' => true, 'message' => 'Campaign deleted']);
    }
    
    public function analytics(AdCampaign $campaign)
    {
        $this->authorize('view', $campaign);
        // Mock analytics data
        return response()->json(['success' => true, 'data' => ['impressions' => 1000, 'clicks' => 50]]);
    }

    public function pause(AdCampaign $campaign)
    {
        $this->authorize('update', $campaign);
        $campaign->update(['status' => 'paused']);
        return response()->json(['success' => true, 'message' => 'Campaign paused']);
    }

    public function resume(AdCampaign $campaign)
    {
        $this->authorize('update', $campaign);
        $campaign->update(['status' => 'active']);
        return response()->json(['success' => true, 'message' => 'Campaign resumed']);
    }
}
