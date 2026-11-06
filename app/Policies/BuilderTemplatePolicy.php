<?php

namespace App\Policies;

use App\Models\BuilderTemplate;
use App\Models\User;

class BuilderTemplatePolicy
{
    public function view(User $user, BuilderTemplate $template): bool
    {
        return $template->workspace_id === null || $user->workspaces()->whereKey($template->workspace_id)->exists();
    }

    public function update(User $user, BuilderTemplate $template): bool
    {
        return ! $template->is_builtin && $user->workspaces()->whereKey($template->workspace_id)->wherePivotIn('role', ['owner', 'admin', 'designer'])->exists();
    }

    public function delete(User $user, BuilderTemplate $template): bool
    {
        return $this->update($user, $template);
    }
}
