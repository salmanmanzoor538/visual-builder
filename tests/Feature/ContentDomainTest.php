<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Website;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ContentDomainTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function owner_can_define_and_populate_a_typed_collection(): void
    {
        [$owner, $website] = $this->site();
        $this->actingAs($owner)->post(route('collections.store', $website), ['name' => 'Projects'])->assertRedirect();
        $collection = $website->cmsCollections()->firstOrFail();
        $this->actingAs($owner)->post(route('fields.store', [$website, $collection]), ['name' => 'Title', 'type' => 'text', 'is_required' => 1])->assertRedirect();
        $this->actingAs($owner)->post(route('items.store', [$website, $collection]), ['slug' => 'first-project', 'values' => ['title' => 'First project']])->assertRedirect();
        $this->assertDatabaseHas('cms_items', ['cms_collection_id' => $collection->id, 'slug' => 'first-project']);
    }

    #[Test]
    public function outsider_cannot_manage_a_sites_collection(): void
    {
        [, $website] = $this->site();
        $outsider = User::factory()->create();
        $this->actingAs($outsider)->post(route('collections.store', $website), ['name' => 'Stolen'])->assertForbidden();
    }

    #[Test]
    public function authorized_user_can_upload_a_validated_image(): void
    {
        Storage::fake('public');
        [$owner, $website] = $this->site();
        $this->actingAs($owner)->post(route('assets.store', $website->workspace), ['file' => UploadedFile::fake()->image('hero.jpg'), 'alt_text' => 'Hero'])->assertRedirect();
        $asset = $website->workspace->assets()->firstOrFail();
        Storage::disk('public')->assertExists($asset->path);
        $this->assertSame('Hero', $asset->alt_text);
    }

    /** @return array{User, Website} */
    private function site(): array
    {
        $owner = User::factory()->create();
        $workspace = Workspace::create(['name' => 'Studio', 'slug' => 'studio']);
        $workspace->users()->attach($owner, ['role' => 'owner']);

        return [$owner, Website::create(['workspace_id' => $workspace->id, 'created_by' => $owner->id, 'name' => 'Site', 'slug' => 'site'])];
    }
}
