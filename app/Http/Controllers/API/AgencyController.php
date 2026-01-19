<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AgencyClient;
use App\Models\Team;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    public function clients()
    {
        $team = auth()->user()->currentTeam;
        
        if (!$team || $team->type !== 'agency') {
            return response()->json(['error' => 'هذه الميزة متاحة للوكالات فقط'], 403);
        }

        $clients = AgencyClient::where('agency_team_id', $team->id)
            ->with('clientTeam:id,name,logo')
            ->get();

        return response()->json($clients);
    }

    public function storeClient(Request $request)
    {
        $team = auth()->user()->currentTeam;
        
        if (!$team || $team->type !== 'agency') {
            return response()->json(['error' => 'هذه الميزة متاحة للوكالات فقط'], 403);
        }

        $validated = $request->validate([
            'client_team_id' => 'required|exists:teams,id',
            'services' => 'nullable|array',
            'contract_start' => 'nullable|date',
            'contract_end' => 'nullable|date|after:contract_start',
            'monthly_retainer' => 'nullable|numeric|min:0',
        ]);

        $client = AgencyClient::create([
            'agency_team_id' => $team->id,
            ...$validated,
            'status' => 'active',
        ]);

        return response()->json(['success' => true, 'data' => $client], 201);
    }

    public function showClient(AgencyClient $client)
    {
        $this->authorizeAgencyAccess($client);
        $client->load(['clientTeam', 'campaigns']);
        return response()->json($client);
    }

    public function updateClient(Request $request, AgencyClient $client)
    {
        $this->authorizeAgencyAccess($client);

        $client->update($request->only([
            'services', 'contract_start', 'contract_end', 
            'monthly_retainer', 'status', 'notes'
        ]));

        return response()->json(['success' => true, 'data' => $client]);
    }

    public function destroyClient(AgencyClient $client)
    {
        $this->authorizeAgencyAccess($client);
        $client->delete();
        return response()->json(['success' => true]);
    }

    public function clientCampaigns(AgencyClient $client)
    {
        $this->authorizeAgencyAccess($client);
        return response()->json($client->campaigns()->with('metrics')->get());
    }

    public function clientAnalytics(AgencyClient $client)
    {
        $this->authorizeAgencyAccess($client);
        
        $analytics = [
            'total_campaigns' => $client->campaigns()->count(),
            'active_campaigns' => $client->campaigns()->where('status', 'active')->count(),
            'total_spend' => $client->campaigns()->sum('budget'),
            'avg_performance' => $client->campaigns()
                ->whereHas('metrics')
                ->with('metrics')
                ->get()
                ->avg(fn($c) => $c->metrics->avg('engagement_rate')),
        ];

        return response()->json($analytics);
    }

    private function authorizeAgencyAccess(AgencyClient $client)
    {
        $team = auth()->user()->currentTeam;
        
        if (!$team || $client->agency_team_id !== $team->id) {
            abort(403, 'غير مصرح بالوصول');
        }
    }
}
