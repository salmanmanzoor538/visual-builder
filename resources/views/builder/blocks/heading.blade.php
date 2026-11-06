@php($level = min(6, max(1, (int) ($node['settings']['level'] ?? 2))))
<h{{ $level }} data-node-id="{{ $node['id'] }}" style="{{ $style }}">{{ $node['content']['text'] ?? '' }}</h{{ $level }}>
