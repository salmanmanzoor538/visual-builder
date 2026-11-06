<x-layouts.app :title="$website->name.' · Content'">
<div class="shell content-shell">
    <div class="page-heading"><a href="{{ route('dashboard') }}">← Websites</a><p class="eyebrow">{{ $website->name }}</p><h1>Content & media</h1><p class="muted">Manage reusable data and files without touching code.</p></div>
    <div class="content-grid">
        <section class="card content-panel"><h2>Media library</h2><form method="POST" enctype="multipart/form-data" action="{{ route('assets.store', $website->workspace) }}" class="stack-form">@csrf<label>Choose media<input type="file" name="file" required></label><label>Alternative text<input name="alt_text" placeholder="Describe the image"></label><button class="button primary">Upload</button></form>
            <div class="asset-grid">@forelse($assets as $asset)<article class="asset">@if(str_starts_with($asset->mime_type, 'image/'))<img src="{{ Storage::disk($asset->disk)->url($asset->path) }}" alt="{{ $asset->alt_text }}">@else<div class="file-icon">FILE</div>@endif<strong title="{{ $asset->original_name }}">{{ Str::limit($asset->original_name, 24) }}</strong><form method="POST" action="{{ route('assets.destroy', $asset) }}">@csrf @method('DELETE')<button class="danger-link">Delete</button></form></article>@empty<p class="muted">No media uploaded yet.</p>@endforelse</div>
        </section>
        <section class="card content-panel"><h2>Visual CMS</h2><form method="POST" action="{{ route('collections.store', $website) }}" class="inline-create">@csrf<input name="name" placeholder="Collection name (e.g. Projects)" required><button class="button primary">Create</button></form>
            @foreach($website->cmsCollections as $collection)<details class="collection" open><summary><strong>{{ $collection->name }}</strong><span>{{ $collection->items->count() }} items</span></summary>
                <form method="POST" action="{{ route('fields.store', [$website, $collection]) }}" class="row-form">@csrf<input name="name" placeholder="Field name" required><select name="type">@foreach($fieldTypes as $type)<option value="{{ $type }}">{{ str($type)->replace('_',' ')->title() }}</option>@endforeach</select><label class="check"><input type="checkbox" name="is_required" value="1">Required</label><button class="button">Add field</button></form>
                <div class="chips">@foreach($collection->fields as $field)<span>{{ $field->name }} · {{ $field->type }}</span>@endforeach</div>
                @if($collection->fields->isNotEmpty())<form method="POST" action="{{ route('items.store', [$website, $collection]) }}" class="stack-form item-form">@csrf<label>Item slug<input name="slug" required placeholder="my-item"></label>@foreach($collection->fields as $field)<label>{{ $field->name }}@if($field->is_required) *@endif<input name="values[{{ $field->key }}]" @required($field->is_required)></label>@endforeach<button class="button primary">Add draft item</button></form>@endif
                <div class="item-list">@foreach($collection->items as $item)<div><span><strong>{{ $item->slug }}</strong> · {{ $item->status }}</span>@if($item->status !== 'published')<form method="POST" action="{{ route('items.publish', [$website, $collection, $item]) }}">@csrf<button class="small-link">Publish item</button></form>@endif</div>@endforeach</div>
            </details>@endforeach
        </section>
    </div>
</div>
</x-layouts.app>
