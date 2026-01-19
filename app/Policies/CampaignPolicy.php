<?php

namespace App\Policies;

use App\Models\Campaign;
use App\Models\User;

class CampaignPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Campaign $campaign): bool
    {
        $team = $user->currentTeam;
        return $team && $campaign->team_id === $team->id;
    }

    public function create(User $user): bool
    {
        $team = $user->currentTeam;
        if (!$team) return false;
        
        $member = $team->getMember($user);
        return $member && in_array($member->role, ['owner', 'admin', 'editor']);
    }

    public function update(User $user, Campaign $campaign): bool
    {
        $team = $user->currentTeam;
        if (!$team || $campaign->team_id !== $team->id) return false;
        
        $member = $team->getMember($user);
        return $member && in_array($member->role, ['owner', 'admin', 'editor']);
    }

    public function delete(User $user, Campaign $campaign): bool
    {
        $team = $user->currentTeam;
        if (!$team || $campaign->team_id !== $team->id) return false;
        
        $member = $team->getMember($user);
        return $member && in_array($member->role, ['owner', 'admin']);
    }
}
