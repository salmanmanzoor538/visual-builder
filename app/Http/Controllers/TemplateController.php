<?php

namespace App\Http\Controllers;

use App\Builder\Services\PageTree;
use App\Builder\Templates\TemplateCatalog;
use App\Builder\Templates\TemplateImporter;
use App\Models\BuilderNode;
use App\Models\BuilderTemplate;
use App\Models\Page;
use App\Models\Website;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function apply(Request $request, Website $website, Page $page, TemplateCatalog $catalog, TemplateImporter $importer): RedirectResponse
    {
        abort_unless($page->website_id === $website->id, 404);
        $this->authorize('update', $page);
        $data = $request->validate(['template' => ['required', 'string'], 'revision' => ['required', 'integer'], 'replace' => ['nullable', 'boolean']]);
        $template = $catalog->find($website->workspace, $data['template']);
        if ($this->containsCustomCode($template['snapshot']['nodes'] ?? [])) {
            $role = $website->workspace->users()->whereKey($request->user()->id)->first()?->pivot->role;
            abort_unless(in_array($role, ['owner', 'admin'], true), 403);
        }
        abort_if($template['scope'] === 'page' && ! $request->boolean('replace') && $page->nodes()->exists(), 422, 'Choose replace or apply this template to a new page.');
        $importer->apply($page, $template['snapshot'], $request->user(), $data['revision'], $request->boolean('replace'));

        return back()->with('status', $template['name'].' applied.');
    }

    public function saveSection(Request $request, Website $website, Page $page, BuilderNode $node, PageTree $trees): RedirectResponse
    {
        abort_unless($page->website_id === $website->id && $node->page_id === $page->id && $node->type === 'section', 404);
        $this->authorize('update', $page);
        $data = $request->validate(['name' => ['required', 'string', 'max:120']]);
        $snapshot = $trees->snapshot($page);
        $section = collect($snapshot['nodes'])->firstWhere('id', $node->id);
        BuilderTemplate::create(['workspace_id' => $website->workspace_id, 'created_by' => $request->user()->id, 'name' => $data['name'], 'scope' => 'section', 'snapshot' => ['schema_version' => 1, 'nodes' => [$section]]]);

        return back()->with('status', 'Section saved to your template library.');
    }

    private function containsCustomCode(array $nodes): bool
    {
        foreach ($nodes as $node) {
            if (($node['type'] ?? null) === 'custom-code' || $this->containsCustomCode($node['children'] ?? [])) {
                return true;
            }
        }

        return false;
    }
}
