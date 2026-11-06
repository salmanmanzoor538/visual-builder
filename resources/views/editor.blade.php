<x-layouts.app :title="$page->name.' · Editor'">
<div class="editor" data-editor data-revision="{{ $page->revision }}">
    <aside class="editor-sidebar library-panel">
        <div class="panel-title"><a href="{{ route('dashboard') }}">← Sites</a><h2>Build</h2><input type="search" placeholder="Search blocks or templates…" data-library-search></div>
        <div class="library-tabs"><button class="active" data-library-tab="blocks">Blocks</button><button data-library-tab="templates">Templates</button></div>
        <div data-library-panel="blocks">
            @foreach(collect($blocks)->groupBy(fn($block) => $block->category()) as $category => $items)
                <div class="block-category"><h3>{{ ucfirst($category) }}</h3>@foreach($items as $block)
                    @if($block->type() !== 'custom-code' || $canCode)
                    <form method="POST" action="{{ route('nodes.store', [$website, $page]) }}" data-library-item="{{ strtolower($block->label().' '.$category) }}">@csrf<input type="hidden" name="type" value="{{ $block->type() }}"><input type="hidden" name="revision" value="{{ $page->revision }}">
                        @if(!in_array('root', $block->allowedParents()))<select name="parent_id" required><option value="">Choose parent…</option>@foreach($nodes->whereIn('type', $block->allowedParents()) as $parent)<option value="{{ $parent->id }}">{{ ucfirst($parent->type) }}</option>@endforeach</select>@endif
                        <button class="block-tile" draggable="true" data-block-type="{{ $block->type() }}"><span>＋</span>{{ $block->label() }}</button>
                    </form>
                    @endif
                @endforeach</div>
            @endforeach
        </div>
        <div data-library-panel="templates" hidden>
            @foreach(collect($templates)->groupBy('scope') as $scope => $items)
                <div class="block-category"><h3>{{ ucfirst($scope) }} templates</h3>@foreach($items as $template)
                    <form method="POST" action="{{ route('templates.apply', [$website, $page]) }}" data-library-item="{{ strtolower($template['name'].' '.$template['category']) }}">@csrf
                        <input type="hidden" name="template" value="{{ $template['key'] }}"><input type="hidden" name="revision" value="{{ $page->revision }}">
                        @if($scope === 'page' && $nodes->isNotEmpty())<label class="template-replace"><input type="checkbox" name="replace" value="1"> Replace page</label>@endif
                        <button class="template-tile" @if($scope === 'page' && $nodes->isNotEmpty()) data-confirm="Replace the current page? A checkpoint will be saved first." @endif><strong>{{ $template['name'] }}</strong><small>{{ ucfirst($template['category']) }}</small></button>
                    </form>
                @endforeach</div>
            @endforeach
        </div>
    </aside>
    <section class="editor-main">
        <div class="editor-toolbar"><div><strong>{{ $website->name }}</strong><span class="muted"> / {{ $page->name }}</span><span class="save-state" data-save-state>All changes saved</span></div>
            <div class="device-switcher"><button data-width="100%" class="active">Desktop</button><button data-width="768px">Tablet</button><button data-width="390px">Mobile</button></div>
            <div class="zoom-control"><button data-zoom="-.1">−</button><output>100%</output><button data-zoom=".1">+</button></div>
            <div class="toolbar-actions"><form method="POST" action="{{ route('pages.checkpoint', [$website, $page]) }}">@csrf<button class="button">Checkpoint</button></form><form method="POST" action="{{ route('websites.publish', $website) }}">@csrf<button class="button primary">Publish</button></form></div>
        </div>
        <div class="canvas-wrap"><div class="canvas" data-canvas tabindex="0">{!! $preview ?: '<div class="canvas-empty"><h2>Start with a template or section</h2><p>Choose a ready-made design or add blocks.</p></div>' !!}</div></div>
    </section>
    <aside class="editor-sidebar inspector"><div class="panel-title"><h2>Layers & properties</h2><p class="muted">Responsive registry-driven controls</p></div>
        <div class="layers">@forelse($nodes as $node)@php($definition = $blocks[$node->type] ?? null)<details class="layer" data-layer-id="{{ $node->id }}" @if($loop->first) open @endif><summary><span>{{ $definition?->label() ?? $node->type }}</span><small>{{ $node->is_hidden ? 'Hidden' : ucfirst($node->type) }}</small></summary>
            @if($definition)<form method="POST" action="{{ route('nodes.update', [$website, $page, $node]) }}" class="property-form" data-autosave>@csrf @method('PATCH')<input type="hidden" name="revision" value="{{ $page->revision }}">
                @foreach($definition->editorSchema() as $path => $field)@php([$group,$key] = explode('.', $path, 2); $value = data_get($node, $path); $name = $group.'['.$key.']'; $control = $field['control'] ?? 'text')
                    @if($node->type !== 'custom-code' || $canCode)<label>{{ $field['label'] ?? ucfirst($key) }}
                        @if(in_array($control, ['textarea','code']))<textarea name="{{ $name }}" rows="{{ $control === 'code' ? 7 : 3 }}" @class(['code-editor' => $control === 'code']) spellcheck="{{ $control === 'code' ? 'false' : 'true' }}">{{ $value }}</textarea>
                        @elseif($control === 'checkbox')<input type="hidden" name="{{ $name }}" value="0"><input type="checkbox" name="{{ $name }}" value="1" @checked($value)>
                        @else<input type="{{ in_array($control, ['color','number','url']) ? $control : 'text' }}" name="{{ $name }}" value="{{ $value }}" @isset($field['min']) min="{{ $field['min'] }}" @endisset @isset($field['max']) max="{{ $field['max'] }}" @endisset>@endif
                    </label>
                    @if(($field['responsive'] ?? false))<div class="responsive-field"><small>Tablet/mobile override</small><div class="field-row"><input type="{{ $control === 'color' ? 'color' : 'number' }}" name="responsive[tablet][{{ $key }}]" value="{{ data_get($node, 'responsive.tablet.'.$key) }}" placeholder="Tablet"><input type="{{ $control === 'color' ? 'color' : 'number' }}" name="responsive[mobile][{{ $key }}]" value="{{ data_get($node, 'responsive.mobile.'.$key) }}" placeholder="Mobile"></div></div>@endif@endif
                @endforeach
                <label class="check"><input type="checkbox" name="is_hidden" value="1" @checked($node->is_hidden)> Hide this block</label>
                @if($node->type === 'custom-code')<p class="code-warning">Runs in an isolated sandbox without access to the page or builder.</p>@endif
                <button class="button primary wide">Apply changes</button>
            </form>@endif
            <div class="layer-actions"><form method="POST" action="{{ route('nodes.duplicate', [$website, $page, $node]) }}">@csrf<input type="hidden" name="revision" value="{{ $page->revision }}"><button class="small-link">Duplicate</button></form>
                @if($node->type === 'section')<form method="POST" action="{{ route('templates.save-section', [$website, $page, $node]) }}">@csrf<input name="name" placeholder="Template name" required><button class="small-link">Save section</button></form>@endif
                <form method="POST" action="{{ route('nodes.destroy', [$website, $page, $node]) }}">@csrf @method('DELETE')<input type="hidden" name="revision" value="{{ $page->revision }}"><button class="danger-link" data-confirm="Delete this block and its children?">Delete</button></form></div>
        </details>@empty<p class="empty-small">No layers yet.</p>@endforelse</div>
    </aside>
</div>
</x-layouts.app>
