<?php

namespace App\Builder\Services;

use App\Builder\Registry\BlockRegistry;
use App\Models\BuilderNode;
use App\Models\Page;
use Illuminate\Support\Collection;

final class PageTree
{
    public function __construct(private BlockRegistry $registry) {}

    public function snapshot(Page $page): array
    {
        $nodes = $page->nodes()->get();

        return [
            'schema_version' => 1,
            'page' => [
                'id' => $page->id,
                'name' => $page->name,
                'slug' => $page->slug,
                'revision' => $page->revision,
                'seo_title' => $page->seo_title,
                'meta_description' => $page->meta_description,
                'canonical_url' => $page->canonical_url,
                'social_image' => $page->social_image,
                'is_indexable' => $page->is_indexable,
                'include_in_sitemap' => $page->include_in_sitemap,
            ],
            'nodes' => $this->children($nodes, null),
        ];
    }

    private function children(Collection $nodes, ?string $parentId): array
    {
        return $nodes->where('parent_id', $parentId)->sortBy('position')->values()->map(function (BuilderNode $node) use ($nodes): array {
            return [
                'id' => $node->id,
                'type' => $node->type,
                'schema_version' => $this->registry->get($node->type)->schemaVersion(),
                'content' => $node->content ?? [],
                'settings' => $node->settings ?? [],
                'styles' => $node->styles ?? [],
                'responsive' => $node->responsive ?? [],
                'is_hidden' => $node->is_hidden,
                'children' => $this->children($nodes, $node->id),
            ];
        })->all();
    }
}
