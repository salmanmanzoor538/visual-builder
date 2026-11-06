<?php

namespace App\Builder\Rendering;

final class StyleCompiler
{
    private const MAP = [
        'backgroundColor' => 'background-color', 'color' => 'color', 'fontSize' => 'font-size',
        'fontWeight' => 'font-weight', 'lineHeight' => 'line-height', 'textAlign' => 'text-align',
        'paddingTop' => 'padding-top', 'paddingRight' => 'padding-right', 'paddingBottom' => 'padding-bottom',
        'paddingLeft' => 'padding-left', 'marginTop' => 'margin-top', 'marginBottom' => 'margin-bottom',
        'borderRadius' => 'border-radius', 'maxWidth' => 'max-width', 'gap' => 'gap', 'opacity' => 'opacity',
    ];

    private const PIXELS = ['fontSize', 'paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft', 'marginTop', 'marginBottom', 'borderRadius', 'maxWidth', 'gap'];

    public function compile(array $styles): string
    {
        $declarations = [];
        foreach ($styles as $property => $value) {
            if (! isset(self::MAP[$property]) || ! is_scalar($value) || $value === '') {
                continue;
            }
            if (in_array($property, self::PIXELS, true) && is_numeric($value)) {
                $value .= 'px';
            }
            $value = preg_replace('/[{}<>;]/', '', (string) $value);
            $declarations[] = self::MAP[$property].':'.$value;
        }

        return implode(';', $declarations);
    }

    public function compileResponsive(string $nodeId, array $responsive): string
    {
        $css = '';
        foreach (['tablet' => 1023, 'mobile' => 639] as $device => $width) {
            $declarations = $this->compile($responsive[$device] ?? []);
            if ($declarations !== '') {
                $safeId = preg_replace('/[^a-zA-Z0-9_-]/', '', $nodeId);
                $css .= "@media(max-width:{$width}px){[data-builder-node=\"{$safeId}\"]>{$this->scopeSelector()}{{$declarations}}}";
            }
        }

        return $css;
    }

    private function scopeSelector(): string
    {
        return ':first-child';
    }
}
