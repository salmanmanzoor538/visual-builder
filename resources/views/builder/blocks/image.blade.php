<figure style="{{ $style }}">
    <img src="{{ $node['content']['url'] ?? '' }}" alt="{{ $node['content']['alt'] ?? '' }}" loading="lazy">
    @if(!empty($node['content']['caption']))<figcaption>{{ $node['content']['caption'] }}</figcaption>@endif
</figure>
