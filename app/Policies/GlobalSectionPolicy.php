<?php

namespace App\Policies;

use App\Models\GlobalSection;
use App\Models\User;

class GlobalSectionPolicy
{
    public function view(User $user, GlobalSection $section): bool
    {
        return $user->workspaces()->whereKey($section->website->workspace_id)->exists();
    }

    public function update(User $user, GlobalSection $section): bool
    {
        return $user->workspaces()->whereKey($section->website->workspace_id)->wherePivotIn('role', ['owner', 'admin', 'designer'])->exists();
    }
}
