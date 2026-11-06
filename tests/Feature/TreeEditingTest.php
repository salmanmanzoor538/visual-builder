<?php

namespace Tests\Feature;

use App\Builder\Services\TreeEditor;
use App\Models\Page;
use App\Models\User;
use App\Models\Website;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TreeEditingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function mutations_reject_a_stale_revision(): void
    {
        [$page] = $this->page();
        $editor = app(TreeEditor::class);
        $editor->create($page, 'section', null, 1);
        $this->expectException(ValidationException::class);
        $editor->create($page, 'section', null, 1);
    }

    #[Test]
    public function duplicating_a_tree_regenerates_every_ulid(): void
    {
        [$page, $owner] = $this->page();
        $editor = app(TreeEditor::class);
        $section = $editor->create($page, 'section', null, 1);
        $heading = $editor->create($page->fresh(), 'heading', $section, 2);
        $copy = $editor->duplicate($page->fresh(), $section->fresh(), $owner, 3);

        $this->assertNotSame($section->id, $copy->id);
        $this->assertNotSame($heading->id, $copy->children()->firstOrFail()->id);
        $this->assertSame(4, $page->fresh()->revision);
        $this->assertDatabaseCount('page_versions', 1);
    }

    /** @return array{Page, User} */
    private function page(): array
    {
        $owner = User::factory()->create();
        $workspace = Workspace::create(['name' => 'Studio', 'slug' => 'studio']);
        $workspace->users()->attach($owner, ['role' => 'owner']);
        $website = Website::create(['workspace_id' => $workspace->id, 'created_by' => $owner->id, 'name' => 'Site', 'slug' => 'site']);

        return [$website->pages()->create(['name' => 'Home', 'slug' => 'home']), $owner];
    }
}
