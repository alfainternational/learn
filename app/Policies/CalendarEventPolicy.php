<?php

namespace App\Policies;

use App\Models\CalendarEvent;
use App\Models\User;

class CalendarEventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CalendarEvent $event): bool
    {
        $team = $user->currentTeam;
        
        // الأحداث الشخصية
        if ($event->user_id === $user->id) return true;
        
        // أحداث الفريق
        if ($team && $event->team_id === $team->id) return true;
        
        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, CalendarEvent $event): bool
    {
        // المنشئ فقط
        if ($event->user_id === $user->id) return true;
        
        // أو admin في الفريق للأحداث الجماعية
        $team = $user->currentTeam;
        if ($team && $event->team_id === $team->id) {
            $member = $team->getMember($user);
            return $member && in_array($member->role, ['owner', 'admin']);
        }
        
        return false;
    }

    public function delete(User $user, CalendarEvent $event): bool
    {
        return $this->update($user, $event);
    }
}
