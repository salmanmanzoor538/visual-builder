<?php

namespace Database\Seeders;

use App\Models\BuilderNode;
use App\Models\Page;
use App\Models\User;
use App\Models\Website;
use App\Models\Workspace;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $owner = User::factory()->create(['name' => 'Demo Owner', 'email' => 'owner@example.test', 'password' => 'password']);
        $workspace = Workspace::create(['name' => 'Acme Studio', 'slug' => 'acme-studio']);
        $workspace->users()->attach($owner, ['role' => 'owner', 'joined_at' => now()]);
        $website = Website::create(['workspace_id' => $workspace->id, 'created_by' => $owner->id, 'name' => 'Acme Launch', 'slug' => 'acme-launch']);
        $page = Page::create(['website_id' => $website->id, 'name' => 'Home', 'slug' => 'home', 'is_homepage' => true]);
        $section = BuilderNode::create(['page_id' => $page->id, 'type' => 'section', 'position' => 0, 'styles' => ['paddingTop' => 80, 'paddingBottom' => 80, 'backgroundColor' => '#f8fafc']]);
        BuilderNode::create(['page_id' => $page->id, 'parent_id' => $section->id, 'type' => 'heading', 'position' => 0, 'content' => ['text' => 'Build the web visually'], 'settings' => ['level' => 1], 'styles' => ['fontSize' => 56, 'fontWeight' => 700, 'textAlign' => 'center']]);
        BuilderNode::create(['page_id' => $page->id, 'parent_id' => $section->id, 'type' => 'text', 'position' => 1, 'content' => ['text' => 'A structured, secure Laravel website builder.'], 'styles' => ['fontSize' => 20, 'textAlign' => 'center']]);
    }
}
