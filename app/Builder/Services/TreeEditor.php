<?php

namespace App\Builder\Services;

use App\Builder\Registry\BlockRegistry;
use App\Models\BuilderNode;
use App\Models\Page;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class TreeEditor
{
    public function __construct(private BlockRegistry $registry, private PageTree $trees) {}

    public function create(Page $page, string $type, ?BuilderNode $parent, int $expectedRevision): BuilderNode
    {
        return DB::transaction(function () use ($page, $type, $parent, $expectedRevision): BuilderNode {
            $locked = $this->lock($page, $expectedRevision);
            $definition = $this->registry->get($type);
            $this->assertParent($locked, $type, $parent);
            $defaults = $definition->defaults();
            $node = $locked->nodes()->create([
                'parent_id' => $parent?->id,
                'type' => $type,
                'position' => $this->nextPosition($locked, $parent?->id),
                ...$defaults,
            ]);
            $locked->increment('revision');

            return $node;
        });
    }

    /** @param array<string, mixed> $attributes */
    public function update(Page $page, BuilderNode $node, array $attributes, int $expectedRevision): void
    {
        DB::transaction(function () use ($page, $node, $attributes, $expectedRevision): void {
            $locked = $this->lock($page, $expectedRevision);
            $this->assertNode($locked, $node);
            $node->update($attributes);
            $locked->increment('revision');
        });
    }

    public function delete(Page $page, BuilderNode $node, int $expectedRevision): void
    {
        DB::transaction(function () use ($page, $node, $expectedRevision): void {
            $locked = $this->lock($page, $expectedRevision);
            $this->assertNode($locked, $node);
            $parentId = $node->parent_id;
            $node->delete();
            $this->normalize($locked, $parentId);
            $locked->increment('revision');
        });
    }

    public function move(Page $page, BuilderNode $node, ?BuilderNode $parent, int $position, int $expectedRevision): void
    {
        DB::transaction(function () use ($page, $node, $parent, $position, $expectedRevision): void {
            $locked = $this->lock($page, $expectedRevision);
            $this->assertNode($locked, $node);
            $this->assertParent($locked, $node->type, $parent);
            abort_if($parent && $this->descendantIds($node)->contains($parent->id), 422, 'A block cannot be moved inside itself.');
            $oldParentId = $node->parent_id;
            $siblings = $locked->nodes()->where('parent_id', $parent?->id)->whereKeyNot($node->id)->get()->values();
            $position = max(0, min($position, $siblings->count()));
            $siblings->splice($position, 0, [$node]);
            foreach ($siblings as $index => $sibling) {
                $sibling->forceFill(['parent_id' => $parent?->id, 'position' => $index])->save();
            }
            if ($oldParentId !== $parent?->id) {
                $this->normalize($locked, $oldParentId);
            }
            $locked->increment('revision');
        });
    }

    public function duplicate(Page $page, BuilderNode $node, User $user, int $expectedRevision): BuilderNode
    {
        return DB::transaction(function () use ($page, $node, $user, $expectedRevision): BuilderNode {
            $locked = $this->lock($page, $expectedRevision);
            $this->assertNode($locked, $node);
            $locked->nodes()->where('parent_id', $node->parent_id)->where('position', '>', $node->position)->increment('position');
            $copy = $this->copyTree($locked, $node, $node->parent_id, $node->position + 1);
            $locked->increment('revision');
            $this->checkpoint($locked->fresh(), $user, 'duplicate');

            return $copy;
        });
    }

    public function checkpoint(Page $page, User $user, string $reason = 'checkpoint'): void
    {
        $page->versions()->updateOrCreate(
            ['revision' => $page->revision],
            ['created_by' => $user->id, 'reason' => $reason, 'snapshot' => $this->trees->snapshot($page)],
        );
    }

    private function lock(Page $page, int $expectedRevision): Page
    {
        $locked = Page::query()->lockForUpdate()->findOrFail($page->id);
        if ($locked->revision !== $expectedRevision) {
            throw ValidationException::withMessages(['revision' => "This page changed in another session. Refresh to continue from revision {$locked->revision}."]);
        }

        return $locked;
    }

    private function assertNode(Page $page, BuilderNode $node): void
    {
        abort_unless($node->page_id === $page->id, 404);
    }

    private function assertParent(Page $page, string $type, ?BuilderNode $parent): void
    {
        if ($parent) {
            $this->assertNode($page, $parent);
        }
        $definition = $this->registry->get($type);
        $parentType = $parent?->type ?? 'root';
        abort_unless(in_array($parentType, $definition->allowedParents(), true), 422, 'This block cannot be placed there.');
        if ($parent) {
            abort_unless(in_array($type, $this->registry->get($parent->type)->allowedChildren(), true), 422, 'The selected container does not accept this block.');
        }
    }

    private function nextPosition(Page $page, ?string $parentId): int
    {
        return ((int) $page->nodes()->where('parent_id', $parentId)->max('position')) + 1;
    }

    private function normalize(Page $page, ?string $parentId): void
    {
        foreach ($page->nodes()->where('parent_id', $parentId)->get() as $position => $node) {
            $node->update(['position' => $position]);
        }
    }

    private function descendantIds(BuilderNode $node): Collection
    {
        $ids = collect();
        foreach ($node->children as $child) {
            $ids->push($child->id);
            $ids = $ids->merge($this->descendantIds($child));
        }

        return $ids;
    }

    private function copyTree(Page $page, BuilderNode $source, ?string $parentId, int $position): BuilderNode
    {
        $copy = $page->nodes()->create(['parent_id' => $parentId, 'type' => $source->type, 'position' => $position, 'content' => $source->content, 'settings' => $source->settings, 'styles' => $source->styles, 'responsive' => $source->responsive, 'is_hidden' => $source->is_hidden]);
        foreach ($source->children as $child) {
            $this->copyTree($page, $child, $copy->id, $child->position);
        }

        return $copy;
    }
}
