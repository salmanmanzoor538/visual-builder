<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Website;

class WebsitePolicy
{
    public function view(User $user, Website $website): bool
    {
        return $user->workspaces()->whereKey($website->workspace_id)->exists();
    }

    public function update(User $user, Website $website): bool
    {
        return $user->workspaces()->whereKey($website->workspace_id)->wherePivotIn('role', ['owner', 'admin', 'designer', 'editor'])->exists();
    }

    public function publish(User $user, Website $website): bool
    {
        return $user->workspaces()->whereKey($website->workspace_id)->wherePivotIn('role', ['owner', 'admin', 'designer'])->exists();
    }

    public function delete(User $user, Website $website): bool
    {
        return $user->workspaces()->whereKey($website->workspace_id)->wherePivotIn('role', ['owner', 'admin'])->exists();
    }
}
