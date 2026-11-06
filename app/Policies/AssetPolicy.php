<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    public function view(User $user, Asset $asset): bool
    {
        return $user->workspaces()->whereKey($asset->workspace_id)->exists();
    }

    public function update(User $user, Asset $asset): bool
    {
        return $this->hasRole($user, $asset, ['owner', 'admin', 'designer', 'editor']);
    }

    public function delete(User $user, Asset $asset): bool
    {
        return $this->hasRole($user, $asset, ['owner', 'admin', 'designer']);
    }

    private function hasRole(User $user, Asset $asset, array $roles): bool
    {
        return $user->workspaces()->whereKey($asset->workspace_id)->wherePivotIn('role', $roles)->exists();
    }
}
