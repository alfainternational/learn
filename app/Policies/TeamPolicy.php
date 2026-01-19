<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function view(User $user, Team $team): bool
    {
        return $team->hasMember($user);
    }

    public function update(User $user, Team $team): bool
    {
        $member = $team->getMember($user);
        return $member && in_array($member->role, ['owner', 'admin']);
    }

    public function delete(User $user, Team $team): bool
    {
        return $team->owner_id === $user->id;
    }

    public function manageMembers(User $user, Team $team): bool
    {
        $member = $team->getMember($user);
        return $member && in_array($member->role, ['owner', 'admin']);
    }
}
