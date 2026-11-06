<?php

namespace App\Policies;

use App\Models\CmsCollection;
use App\Models\User;

class CmsCollectionPolicy
{
    public function view(User $user, CmsCollection $collection): bool
    {
        return $user->workspaces()->whereKey($collection->website->workspace_id)->exists();
    }

    public function update(User $user, CmsCollection $collection): bool
    {
        return $user->workspaces()->whereKey($collection->website->workspace_id)->wherePivotIn('role', ['owner', 'admin', 'designer', 'editor', 'marketer'])->exists();
    }

    public function delete(User $user, CmsCollection $collection): bool
    {
        return $user->workspaces()->whereKey($collection->website->workspace_id)->wherePivotIn('role', ['owner', 'admin'])->exists();
    }
}
