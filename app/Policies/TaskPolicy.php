<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        $team = $user->currentTeam;
        if (!$team) return false;
        
        return $task->team_id === $team->id || $task->assigned_to === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->currentTeam !== null;
    }

    public function update(User $user, Task $task): bool
    {
        $team = $user->currentTeam;
        if (!$team) return false;
        
        // المنشئ أو المكلف يمكنه التعديل
        if ($task->created_by === $user->id || $task->assigned_to === $user->id) {
            return true;
        }
        
        // أو admin/owner في الفريق
        $member = $team->getMember($user);
        return $member && in_array($member->role, ['owner', 'admin']);
    }

    public function delete(User $user, Task $task): bool
    {
        $team = $user->currentTeam;
        if (!$team) return false;
        
        if ($task->created_by === $user->id) return true;
        
        $member = $team->getMember($user);
        return $member && in_array($member->role, ['owner', 'admin']);
    }
}
