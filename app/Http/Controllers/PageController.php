<?php

namespace App\Http\Controllers;

use App\Builder\Services\PageTree;
use App\Builder\Templates\TemplateImporter;
use App\Models\Page;
use App\Models\Website;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function store(Request $request, Website $website): RedirectResponse
    {
        $this->authorize('update', $website);
        $data = $request->validate(['name' => ['required', 'string', 'max:120']]);
        $base = Str::slug($data['name']) ?: 'page';
        $slug = $base;
        for ($suffix = 2; $website->pages()->where('slug', $slug)->exists(); $suffix++) {
            $slug = $base.'-'.$suffix;
        }
        $page = $website->pages()->create(['name' => $data['name'], 'slug' => $slug, 'position' => $website->pages()->max('position') + 1]);

        return redirect()->route('editor', [$website, $page])->with('status', 'Page created.');
    }

    public function update(Request $request, Website $website, Page $page): RedirectResponse
    {
        abort_unless($page->website_id === $website->id, 404);
        $this->authorize('update', $page);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'], 'slug' => ['required', 'alpha_dash', 'max:120', 'unique:pages,slug,'.$page->id.',id,website_id,'.$website->id],
            'seo_title' => ['nullable', 'string', 'max:70'], 'meta_description' => ['nullable', 'string', 'max:170'],
            'canonical_url' => ['nullable', 'url:http,https', 'max:2048'], 'social_image' => ['nullable', 'url:http,https', 'max:2048'],
        ]);
        $page->update([...$data, 'is_visible' => $request->boolean('is_visible'), 'is_indexable' => $request->boolean('is_indexable'), 'include_in_sitemap' => $request->boolean('include_in_sitemap')]);

        return back()->with('status', 'Page settings saved.');
    }

    public function duplicate(Request $request, Website $website, Page $page, PageTree $trees, TemplateImporter $importer): RedirectResponse
    {
        abort_unless($page->website_id === $website->id, 404);
        $this->authorize('update', $page);
        $copy = DB::transaction(function () use ($website, $page, $request, $trees, $importer): Page {
            $copy = $website->pages()->create(['name' => $page->name.' copy', 'slug' => $page->slug.'-'.strtolower(Str::random(5)), 'position' => ((int) $website->pages()->max('position')) + 1]);
            $importer->apply($copy, $trees->snapshot($page), $request->user(), 1);

            return $copy;
        });

        return redirect()->route('editor', [$website, $copy])->with('status', 'Page duplicated.');
    }

    public function destroy(Website $website, Page $page): RedirectResponse
    {
        abort_unless($page->website_id === $website->id, 404);
        $this->authorize('update', $page);
        abort_if($website->pages()->count() <= 1, 422, 'A website must keep at least one page.');
        $page->delete();

        return redirect()->route('editor', [$website, $website->pages()->firstOrFail()])->with('status', 'Page moved to trash.');
    }
}
