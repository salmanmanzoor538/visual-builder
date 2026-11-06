<?php

namespace App\Http\Controllers;

use App\Builder\Registry\BlockRegistry;
use App\Builder\Rendering\BuilderRenderer;
use App\Builder\Services\PageTree;
use App\Builder\Services\TreeEditor;
use App\Builder\Templates\TemplateCatalog;
use App\Models\Page;
use App\Models\Website;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EditorController extends Controller
{
    public function __invoke(Website $website, Page $page, BlockRegistry $registry, PageTree $trees, BuilderRenderer $renderer, TemplateCatalog $templates): View
    {
        abort_unless($page->website_id === $website->id, 404);
        $this->authorize('update', $page);
        $snapshot = $trees->snapshot($page);

        $role = $website->workspace->users()->whereKey(request()->user()->id)->first()?->pivot->role;

        return view('editor', ['website' => $website, 'page' => $page, 'blocks' => $registry->all(), 'nodes' => $page->nodes()->get(), 'preview' => $renderer->renderTree($snapshot['nodes']), 'templates' => $templates->all($website->workspace), 'canCode' => in_array($role, ['owner', 'admin'], true)]);
    }

    public function checkpoint(Request $request, Website $website, Page $page, TreeEditor $editor): RedirectResponse
    {
        abort_unless($page->website_id === $website->id, 404);
        $this->authorize('update', $page);
        $editor->checkpoint($page, $request->user(), 'manual');

        return back()->with('status', 'Version checkpoint saved.');
    }
}
