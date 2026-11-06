<?php

namespace Tests\Feature;

use App\Builder\Services\TreeEditor;
use App\Models\User;
use App\Models\Website;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomCodeAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function editors_cannot_change_custom_code(): void
    {
        $owner = User::factory()->create();
        $editorUser = User::factory()->create();
        $workspace = Workspace::create(['name' => 'Studio', 'slug' => 'studio']);
        $workspace->users()->attach($owner, ['role' => 'owner']);
        $workspace->users()->attach($editorUser, ['role' => 'editor']);
        $website = Website::create(['workspace_id' => $workspace->id, 'created_by' => $owner->id, 'name' => 'Site', 'slug' => 'site']);
        $page = $website->pages()->create(['name' => 'Home', 'slug' => 'home']);
        $tree = app(TreeEditor::class);
        $section = $tree->create($page, 'section', null, 1);
        $code = $tree->create($page->fresh(), 'custom-code', $section, 2);

        $this->actingAs($editorUser)->patch(route('nodes.update', [$website, $page, $code]), [
            'revision' => 3, 'content' => ['html' => '<p>Changed</p>'],
        ])->assertForbidden();
        $this->assertSame('<p>Custom content</p>', $code->fresh()->content['html']);
    }
}
