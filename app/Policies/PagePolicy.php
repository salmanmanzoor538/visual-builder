<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;

class PagePolicy
{
    public function view(User $user, Page $page): bool
    {
        return $user->workspaces()->whereKey($page->website->workspace_id)->exists();
    }

    public function update(User $user, Page $page): bool
    {
        return $user->workspaces()->whereKey($page->website->workspace_id)->wherePivotIn('role', ['owner', 'admin', 'designer', 'editor'])->exists();
    }
}
