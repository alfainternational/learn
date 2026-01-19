<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function index()
    {
        $teams = auth()->user()->teams()->with('owner:id,name,email')->get();
        return response()->json($teams);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'in:business,agency',
        ]);

        $team = Team::create([
            ...$validated,
            'owner_id' => auth()->id(),
        ]);

        // إضافة المالك كعضو
        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => auth()->id(),
            'role' => 'owner',
        ]);

        // تحديث المستخدم
        auth()->user()->update([
            'current_team_id' => $team->id,
            'account_type' => $validated['type'] ?? 'business',
        ]);

        return response()->json(['success' => true, 'data' => $team], 201);
    }

    public function show(Team $team)
    {
        $this->authorize('view', $team);
        $team->load(['members.user', 'owner']);
        return response()->json($team);
    }

    public function update(Request $request, Team $team)
    {
        $this->authorize('update', $team);
        $team->update($request->only(['name', 'description', 'logo', 'settings']));
        return response()->json(['success' => true, 'data' => $team]);
    }

    public function destroy(Team $team)
    {
        $this->authorize('delete', $team);
        $team->delete();
        return response()->json(['success' => true]);
    }

    // إدارة الأعضاء
    public function members(Team $team)
    {
        $this->authorize('view', $team);
        return response()->json($team->members()->with('user')->get());
    }

    public function invite(Request $request, Team $team)
    {
        $this->authorize('update', $team);
        
        $validated = $request->validate([
            'email' => 'required|email',
            'role' => 'in:admin,editor,viewer',
        ]);

        // التحقق من عدم وجود دعوة سابقة
        $existing = TeamInvitation::where('team_id', $team->id)
            ->where('email', $validated['email'])
            ->whereNull('accepted_at')
            ->first();

        if ($existing) {
            return response()->json(['error' => 'توجد دعوة سابقة لهذا البريد'], 400);
        }

        $invitation = TeamInvitation::create([
            'team_id' => $team->id,
            'invited_by' => auth()->id(),
            'email' => $validated['email'],
            'role' => $validated['role'] ?? 'editor',
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);

        return response()->json(['success' => true, 'data' => $invitation]);
    }

    public function acceptInvitation(Request $request)
    {
        $invitation = TeamInvitation::where('token', $request->token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        // إضافة العضو
        TeamMember::create([
            'team_id' => $invitation->team_id,
            'user_id' => auth()->id(),
            'role' => $invitation->role,
        ]);

        $invitation->update(['accepted_at' => now()]);

        return response()->json(['success' => true, 'team' => $invitation->team]);
    }

    public function updateMemberRole(Request $request, Team $team, TeamMember $member)
    {
        $this->authorize('update', $team);
        
        if ($member->role === 'owner') {
            return response()->json(['error' => 'لا يمكن تعديل دور المالك'], 400);
        }

        $member->update(['role' => $request->role]);
        return response()->json(['success' => true]);
    }

    public function removeMember(Team $team, TeamMember $member)
    {
        $this->authorize('update', $team);
        
        if ($member->role === 'owner') {
            return response()->json(['error' => 'لا يمكن إزالة المالك'], 400);
        }

        $member->delete();
        return response()->json(['success' => true]);
    }

    public function switchTeam(Team $team)
    {
        $member = TeamMember::where('team_id', $team->id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$member) {
            return response()->json(['error' => 'لست عضواً في هذا الفريق'], 403);
        }

        auth()->user()->update(['current_team_id' => $team->id]);
        return response()->json(['success' => true]);
    }
}
