<?php

namespace Tests\Feature;

use App\Builder\Templates\TemplateCatalog;
use App\Builder\Templates\TemplateImporter;
use App\Models\User;
use App\Models\Website;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TemplateFeatureTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function applying_a_template_generates_new_node_ids_and_a_checkpoint(): void
    {
        [$owner, $workspace, $website] = $this->site();
        $page = $website->pages()->create(['name' => 'Home', 'slug' => 'home']);
        $template = app(TemplateCatalog::class)->find($workspace, 'section-hero');
        app(TemplateImporter::class)->apply($page, $template['snapshot'], $owner, 1);

        $this->assertSame(5, $page->nodes()->count());
        $this->assertSame(2, $page->fresh()->revision);
        $this->assertDatabaseHas('page_versions', ['page_id' => $page->id, 'reason' => 'template']);
    }

    #[Test]
    public function a_user_cannot_apply_a_template_to_another_tenant_page(): void
    {
        [$owner, , $website] = $this->site();
        [, , $otherWebsite] = $this->site('other');
        $page = $otherWebsite->pages()->create(['name' => 'Home', 'slug' => 'home']);

        $this->actingAs($owner)->post(route('templates.apply', [$website, $page]), ['template' => 'section-hero', 'revision' => 1])->assertNotFound();
    }

    private function site(string $suffix = 'main'): array
    {
        $owner = User::factory()->create();
        $workspace = Workspace::create(['name' => 'Studio '.$suffix, 'slug' => 'studio-'.$suffix]);
        $workspace->users()->attach($owner, ['role' => 'owner']);
        $website = Website::create(['workspace_id' => $workspace->id, 'created_by' => $owner->id, 'name' => 'Site', 'slug' => 'site-'.$suffix]);

        return [$owner, $workspace, $website];
    }
}
