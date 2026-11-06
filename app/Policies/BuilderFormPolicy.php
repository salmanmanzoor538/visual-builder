<?php

namespace App\Policies;

use App\Models\BuilderForm;
use App\Models\User;

class BuilderFormPolicy
{
    public function view(User $user, BuilderForm $form): bool
    {
        return $user->workspaces()->whereKey($form->website->workspace_id)->exists();
    }

    public function update(User $user, BuilderForm $form): bool
    {
        return $user->workspaces()->whereKey($form->website->workspace_id)->wherePivotIn('role', ['owner', 'admin', 'designer', 'editor'])->exists();
    }

    public function viewSubmissions(User $user, BuilderForm $form): bool
    {
        return $user->workspaces()->whereKey($form->website->workspace_id)->wherePivotIn('role', ['owner', 'admin'])->exists();
    }
}
