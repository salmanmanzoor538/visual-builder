<?php

namespace App\Http\Controllers;

use App\Builder\Registry\BlockRegistry;
use App\Builder\Security\CustomCodeSanitizer;
use App\Builder\Services\TreeEditor;
use App\Models\BuilderNode;
use App\Models\Page;
use App\Models\Website;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NodeController extends Controller
{
    public function store(Request $request, Website $website, Page $page, BlockRegistry $registry, TreeEditor $editor): RedirectResponse
    {
        $this->guardPage($website, $page);
        $data = $request->validate(['type' => ['required', 'string', Rule::in(array_keys($registry->all()))], 'parent_id' => ['nullable', 'string'], 'revision' => ['required', 'integer', 'min:1']]);
        if ($data['type'] === 'custom-code') {
            $this->authorizeCustomCode($request, $website);
        }
        $parent = isset($data['parent_id']) ? $page->nodes()->findOrFail($data['parent_id']) : null;
        $editor->create($page, $data['type'], $parent, $data['revision']);

        return back()->with('status', $registry->get($data['type'])->label().' added.');
    }

    public function update(Request $request, Website $website, Page $page, BuilderNode $node, BlockRegistry $registry, TreeEditor $editor, CustomCodeSanitizer $sanitizer): RedirectResponse
    {
        $this->guardNode($website, $page, $node);
        $request->validate(['revision' => ['required', 'integer', 'min:1']]);
        $payload = [
            'content' => array_merge($node->content ?? [], array_filter($request->input('content', []), fn ($value) => $value !== null)),
            'settings' => array_merge($node->settings ?? [], array_filter($request->input('settings', []), fn ($value) => $value !== null)),
            'styles' => array_merge($node->styles ?? [], array_filter($request->input('styles', []), fn ($value) => $value !== null && $value !== '')),
            'responsive' => array_replace_recursive($node->responsive ?? [], $request->input('responsive', [])),
            'is_hidden' => $request->boolean('is_hidden'),
        ];
        if ($node->type === 'custom-code') {
            $this->authorizeCustomCode($request, $website);
            $payload['content']['html'] = $sanitizer->html((string) ($payload['content']['html'] ?? ''));
            $payload['content']['css'] = $sanitizer->css((string) ($payload['content']['css'] ?? ''));
            $payload['content']['javascript'] = $sanitizer->javascript((string) ($payload['content']['javascript'] ?? ''));
        }
        Validator::make($payload, $registry->get($node->type)->rules())->validate();
        $editor->update($page, $node, $payload, $request->integer('revision'));

        return back()->with('status', 'Block updated.');
    }

    public function destroy(Request $request, Website $website, Page $page, BuilderNode $node, TreeEditor $editor): RedirectResponse
    {
        $this->guardNode($website, $page, $node);
        $request->validate(['revision' => ['required', 'integer', 'min:1']]);
        $editor->delete($page, $node, $request->integer('revision'));

        return back()->with('status', 'Block deleted.');
    }

    public function move(Request $request, Website $website, Page $page, BuilderNode $node, TreeEditor $editor): RedirectResponse
    {
        $this->guardNode($website, $page, $node);
        $data = $request->validate(['parent_id' => ['nullable', 'string'], 'position' => ['required', 'integer', 'min:0'], 'revision' => ['required', 'integer', 'min:1']]);
        $parent = isset($data['parent_id']) ? $page->nodes()->findOrFail($data['parent_id']) : null;
        $editor->move($page, $node, $parent, $data['position'], $data['revision']);

        return back()->with('status', 'Block moved.');
    }

    public function duplicate(Request $request, Website $website, Page $page, BuilderNode $node, TreeEditor $editor): RedirectResponse
    {
        $this->guardNode($website, $page, $node);
        $data = $request->validate(['revision' => ['required', 'integer', 'min:1']]);
        $editor->duplicate($page, $node, $request->user(), $data['revision']);

        return back()->with('status', 'Block duplicated.');
    }

    private function guardPage(Website $website, Page $page): void
    {
        abort_unless($page->website_id === $website->id, 404);
        $this->authorize('update', $page);
    }

    private function guardNode(Website $website, Page $page, BuilderNode $node): void
    {
        $this->guardPage($website, $page);
        abort_unless($node->page_id === $page->id, 404);
    }

    private function authorizeCustomCode(Request $request, Website $website): void
    {
        $role = $website->workspace->users()->whereKey($request->user()->id)->first()?->pivot->role;
        abort_unless(in_array($role, ['owner', 'admin'], true), 403);
    }
}
