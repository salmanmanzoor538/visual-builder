<div data-block="{{ $node['type'] }}" style="{{ $style }}">
    @if(isset($node['content']['text']))<p>{{ $node['content']['text'] }}</p>@endif
    {!! $children !!}
</div>
