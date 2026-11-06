<?php

namespace App\Builder\Actions;

use App\Builder\Rendering\BuilderRenderer;
use App\Builder\Services\PageTree;
use App\Models\PublishVersion;
use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class PublishWebsite
{
    public function __construct(private PageTree $trees, private BuilderRenderer $renderer) {}

    public function execute(Website $website, User $publisher): PublishVersion
    {
        return DB::transaction(function () use ($website, $publisher): PublishVersion {
            $lockedWebsite = Website::query()->lockForUpdate()->findOrFail($website->id);
            $nextVersion = ((int) $lockedWebsite->publishVersions()->max('version')) + 1;
            $version = $lockedWebsite->publishVersions()->create([
                'published_by' => $publisher->id,
                'version' => $nextVersion,
                'status' => 'ready',
                'manifest' => ['schema_version' => 1],
                'published_at' => now(),
            ]);

            foreach ($lockedWebsite->pages()->where('is_visible', true)->get() as $page) {
                $snapshot = $this->trees->snapshot($page);
                $version->pages()->create([
                    'page_id' => $page->id,
                    'slug' => $page->slug,
                    'snapshot' => $snapshot,
                    'rendered_html' => $this->renderer->renderTree($snapshot['nodes']),
                    'rendered_css' => '',
                ]);
            }

            $lockedWebsite->forceFill([
                'active_publish_version_id' => $version->id,
                'status' => 'published',
                'published_at' => now(),
            ])->save();

            Cache::forget("published-website:{$lockedWebsite->workspace_id}:{$lockedWebsite->id}");

            return $version->load('pages');
        });
    }
}
