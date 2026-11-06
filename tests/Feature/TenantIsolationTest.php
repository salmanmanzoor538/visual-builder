<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Website;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_cannot_view_another_workspaces_website(): void
    {
        $owner = User::factory()->create();
        $outsider = User::factory()->create();
        $workspace = Workspace::create(['name' => 'Private', 'slug' => 'private']);
        $workspace->users()->attach($owner, ['role' => 'owner']);
        $website = Website::create(['workspace_id' => $workspace->id, 'created_by' => $owner->id, 'name' => 'Site', 'slug' => 'site']);

        $this->assertTrue($owner->can('view', $website));
        $this->assertFalse($outsider->can('view', $website));
    }
}
