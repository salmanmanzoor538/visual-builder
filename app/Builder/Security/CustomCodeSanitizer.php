<?php

namespace App\Builder\Security;

use Illuminate\Validation\ValidationException;

final class CustomCodeSanitizer
{
    public function html(string $html): string
    {
        if (mb_strlen($html) > 50000) {
            throw ValidationException::withMessages(['content.html' => 'Custom HTML may not exceed 50 KB.']);
        }

        $html = preg_replace('#<(script|iframe|object|embed|base|meta|link)[^>]*>.*?</\1>#is', '', $html) ?? '';
        $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? '';
        $html = preg_replace('/\s(href|src)\s*=\s*(["\'])\s*(javascript:|data:text\/html)[^"\']*\2/i', ' $1="#"', $html) ?? '';

        return strip_tags($html, '<div><section><article><header><footer><main><aside><nav><span><p><h1><h2><h3><h4><h5><h6><a><img><picture><source><ul><ol><li><strong><em><small><blockquote><figure><figcaption><table><thead><tbody><tr><th><td><button><form><label><input><textarea><select><option><br><hr>');
    }

    public function css(string $css): string
    {
        if (mb_strlen($css) > 50000 || preg_match('/@import|expression\s*\(|javascript:|behavior\s*:/i', $css)) {
            throw ValidationException::withMessages(['content.css' => 'Custom CSS contains a forbidden construct or is too large.']);
        }

        return str_replace('</style', '<\/style', $css);
    }

    public function javascript(string $javascript): string
    {
        if (mb_strlen($javascript) > 50000 || preg_match('/<\/script/i', $javascript)) {
            throw ValidationException::withMessages(['content.javascript' => 'Custom JavaScript is invalid or too large.']);
        }

        return $javascript;
    }
}
