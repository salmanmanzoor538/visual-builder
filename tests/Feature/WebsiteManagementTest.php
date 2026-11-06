<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Website;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WebsiteManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function registration_creates_an_owned_workspace(): void
    {
        $this->post('/register', [
            'name' => 'Jamie', 'workspace_name' => 'Jamie Studio', 'email' => 'jamie@example.test',
            'password' => 'secure-password', 'password_confirmation' => 'secure-password',
        ])->assertRedirect(route('dashboard'));

        $user = User::where('email', 'jamie@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('owner', $user->workspaces()->firstOrFail()->pivot->role);
    }

    #[Test]
    public function an_owner_can_create_a_website_with_a_homepage(): void
    {
        [$owner, $workspace] = $this->ownedWorkspace();
        $this->actingAs($owner)->post(route('websites.store', $workspace), ['name' => 'My Brand'])->assertRedirect();
        $site = Website::where('workspace_id', $workspace->id)->firstOrFail();
        $this->assertDatabaseHas('pages', ['website_id' => $site->id, 'slug' => 'home', 'is_homepage' => true]);
    }

    #[Test]
    public function an_outsider_cannot_create_a_website_in_another_workspace(): void
    {
        [, $workspace] = $this->ownedWorkspace();
        $this->actingAs(User::factory()->create())->post(route('websites.store', $workspace), ['name' => 'Stolen'])->assertForbidden();
        $this->assertDatabaseMissing('websites', ['name' => 'Stolen']);
    }

    #[Test]
    public function a_node_cannot_be_mutated_through_another_page_route(): void
    {
        [$owner, $workspace] = $this->ownedWorkspace();
        $site = Website::create(['workspace_id' => $workspace->id, 'created_by' => $owner->id, 'name' => 'Site', 'slug' => 'site']);
        $page = $site->pages()->create(['name' => 'One', 'slug' => 'one']);
        $other = $site->pages()->create(['name' => 'Two', 'slug' => 'two']);
        $node = $page->nodes()->create(['type' => 'section', 'position' => 0]);
        $this->actingAs($owner)->delete(route('nodes.destroy', [$site, $other, $node]))->assertNotFound();
        $this->assertDatabaseHas('builder_nodes', ['id' => $node->id]);
    }

    /** @return array{User, Workspace} */
    private function ownedWorkspace(): array
    {
        $owner = User::factory()->create();
        $workspace = Workspace::create(['name' => 'Studio', 'slug' => 'studio']);
        $workspace->users()->attach($owner, ['role' => 'owner', 'joined_at' => now()]);

        return [$owner, $workspace];
    }
}
