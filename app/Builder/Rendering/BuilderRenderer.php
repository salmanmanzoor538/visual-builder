<?php

namespace App\Builder\Rendering;

use App\Builder\Registry\BlockRegistry;
use Illuminate\Contracts\View\Factory;

final class BuilderRenderer
{
    public function __construct(private BlockRegistry $registry, private Factory $views, private StyleCompiler $styles) {}

    public function renderTree(array $nodes): string
    {
        return collect($nodes)->filter(fn (array $node) => ! ($node['is_hidden'] ?? false))->map(fn (array $node) => $this->renderNode($node))->implode('');
    }

    public function renderNode(array $node): string
    {
        $block = $this->registry->get($node['type']);

        $html = $this->views->make($block->view(), [
            'node' => $node,
            'children' => $this->renderTree($node['children'] ?? []),
            'style' => $this->styles->compile($node['styles'] ?? []),
        ])->render();
        $id = htmlspecialchars((string) ($node['id'] ?? ''), ENT_QUOTES, 'UTF-8');
        $responsive = $this->styles->compileResponsive($id, $node['responsive'] ?? []);

        return '<div data-builder-node="'.$id.'">'.$html.($responsive !== '' ? '<style>'.$responsive.'</style>' : '').'</div>';
    }
}
