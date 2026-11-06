@php
    $html = app(\App\Builder\Security\CustomCodeSanitizer::class)->html((string) data_get($node, 'content.html', ''));
    $css = app(\App\Builder\Security\CustomCodeSanitizer::class)->css((string) data_get($node, 'content.css', ''));
    $js = data_get($node, 'settings.execute') ? app(\App\Builder\Security\CustomCodeSanitizer::class)->javascript((string) data_get($node, 'content.javascript', '')) : '';
    $document = '<!doctype html><html><head><meta charset="utf-8"><meta http-equiv="Content-Security-Policy" content="default-src \'none\'; img-src https: data:; style-src \'unsafe-inline\'; script-src \'unsafe-inline\'; font-src https: data:; connect-src \'none\'"><style>body{margin:0}'.$css.'</style></head><body>'.$html.($js !== '' ? '<script>'.$js.'<\/script>' : '').'</body></html>';
@endphp
<iframe title="Custom code block" sandbox="allow-scripts" loading="lazy" referrerpolicy="no-referrer" srcdoc="{{ $document }}" style="width:100%;border:0;{{ $style }}"></iframe>
