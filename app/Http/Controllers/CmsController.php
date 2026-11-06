<?php

namespace App\Http\Controllers;

use App\Cms\CmsValueValidator;
use App\Models\CmsCollection;
use App\Models\CmsItem;
use App\Models\Website;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CmsController extends Controller
{
    public function storeCollection(Request $request, Website $website): RedirectResponse
    {
        $this->authorize('update', $website);
        $data = $request->validate(['name' => ['required', 'string', 'max:120']]);
        $base = Str::slug($data['name']) ?: 'collection';
        $slug = $base;
        for ($i = 2; $website->cmsCollections()->where('slug', $slug)->exists(); $i++) {
            $slug = $base.'-'.$i;
        }
        $website->cmsCollections()->create(['name' => $data['name'], 'slug' => $slug]);

        return back()->with('status', 'Collection created.');
    }

    public function storeField(Request $request, Website $website, CmsCollection $collection, CmsValueValidator $validator): RedirectResponse
    {
        $this->guardCollection($website, $collection);
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'type' => ['required', Rule::in($validator->types())], 'is_required' => ['nullable', 'boolean']]);
        $base = Str::snake($data['name']) ?: 'field';
        $key = $base;
        for ($i = 2; $collection->fields()->where('key', $key)->exists(); $i++) {
            $key = $base.'_'.$i;
        }
        $collection->fields()->create(['name' => $data['name'], 'key' => $key, 'type' => $data['type'], 'position' => $collection->fields()->max('position') + 1, 'is_required' => $request->boolean('is_required')]);
        $collection->increment('schema_version');

        return back()->with('status', 'Collection field added.');
    }

    public function storeItem(Request $request, Website $website, CmsCollection $collection, CmsValueValidator $validator): RedirectResponse
    {
        $this->guardCollection($website, $collection);
        $request->validate(['slug' => ['required', 'alpha_dash:ascii', 'max:160']]);
        $slug = Str::slug($request->string('slug'));
        abort_if($collection->items()->where('slug', $slug)->exists(), 422, 'That item slug is already used.');
        $values = $validator->validate($collection->load('fields'), $request->input('values', []));
        $collection->items()->create(['created_by' => $request->user()->id, 'slug' => $slug, 'values' => $values]);

        return back()->with('status', 'Draft content item created.');
    }

    public function publishItem(Request $request, Website $website, CmsCollection $collection, CmsItem $item): RedirectResponse
    {
        $this->guardCollection($website, $collection);
        abort_unless($item->cms_collection_id === $collection->id, 404);
        $item->update(['status' => 'published', 'published_at' => now()]);

        return back()->with('status', 'Content item published. Republish the website to update its live snapshot.');
    }

    private function guardCollection(Website $website, CmsCollection $collection): void
    {
        abort_unless($collection->website_id === $website->id, 404);
        $this->authorize('update', $collection);
    }
}
