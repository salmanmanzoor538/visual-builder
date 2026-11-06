<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $publishedPage->snapshot['page']['seo_title'] ?: $publishedPage->snapshot['page']['name'] }}</title>
    @if($publishedPage->snapshot['page']['meta_description'])<meta name="description" content="{{ $publishedPage->snapshot['page']['meta_description'] }}">@endif
    @if(!($publishedPage->snapshot['page']['is_indexable'] ?? true))<meta name="robots" content="noindex,nofollow">@endif
    @if($publishedPage->snapshot['page']['canonical_url'] ?? null)<link rel="canonical" href="{{ $publishedPage->snapshot['page']['canonical_url'] }}">@endif
    <meta property="og:title" content="{{ $publishedPage->snapshot['page']['seo_title'] ?: $publishedPage->snapshot['page']['name'] }}">
    @if($publishedPage->snapshot['page']['meta_description'])<meta property="og:description" content="{{ $publishedPage->snapshot['page']['meta_description'] }}">@endif
    @if($publishedPage->snapshot['page']['social_image'] ?? null)<meta property="og:image" content="{{ $publishedPage->snapshot['page']['social_image'] }}">@endif
    @if($publishedPage->rendered_css)<style>{{ $publishedPage->rendered_css }}</style>@endif
</head>
<body>{!! $publishedPage->rendered_html !!}</body>
</html>
