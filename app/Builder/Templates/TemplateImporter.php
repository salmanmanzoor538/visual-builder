<?php

namespace App\Builder\Templates;

use App\Builder\Registry\BlockRegistry;
use App\Builder\Services\TreeEditor;
use App\Models\BuilderNode;
use App\Models\Page;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class TemplateImporter
{
    public function __construct(private BlockRegistry $registry, private TreeEditor $editor) {}

    public function apply(Page $page, array $snapshot, User $user, int $expectedRevision, bool $replace = false): void
    {
        DB::transaction(function () use ($page, $snapshot, $user, $expectedRevision, $replace): void {
            $locked = Page::query()->lockForUpdate()->findOrFail($page->id);
            if ($locked->revision !== $expectedRevision) {
                throw ValidationException::withMessages(['revision' => 'The page changed. Refresh before applying a template.']);
            }
            if ($replace) {
                $this->editor->checkpoint($locked, $user, 'before-template');
                $locked->nodes()->delete();
            }
            foreach ($snapshot['nodes'] ?? [] as $position => $node) {
                $this->insert($locked, $node, null, $position);
            }
            $locked->increment('revision');
            $this->editor->checkpoint($locked->fresh(), $user, 'template');
        });
    }

    private function insert(Page $page, array $source, ?BuilderNode $parent, int $position): BuilderNode
    {
        $type = (string) ($source['type'] ?? '');
        $definition = $this->registry->get($type);
        $parentType = $parent?->type ?? 'root';
        if (! in_array($parentType, $definition->allowedParents(), true) || ($parent && ! in_array($type, $this->registry->get($parent->type)->allowedChildren(), true))) {
            throw ValidationException::withMessages(['template' => "Invalid {$type} placement in template."]);
        }
        $payload = [
            'content' => $source['content'] ?? [], 'settings' => $source['settings'] ?? [],
            'styles' => $source['styles'] ?? [], 'responsive' => $source['responsive'] ?? [],
        ];
        validator($payload, $definition->rules())->validate();
        $node = $page->nodes()->create([...$payload, 'parent_id' => $parent?->id, 'type' => $type, 'position' => $position, 'is_hidden' => false]);
        foreach ($source['children'] ?? [] as $childPosition => $child) {
            $this->insert($page, $child, $node, $childPosition);
        }

        return $node;
    }
}
