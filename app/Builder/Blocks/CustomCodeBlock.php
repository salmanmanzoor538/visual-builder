<?php

namespace App\Builder\Blocks;

use App\Builder\Blocks\Concerns\HasDefaultBlockMetadata;
use App\Builder\Contracts\BlockDefinition;

final class CustomCodeBlock implements BlockDefinition
{
    use HasDefaultBlockMetadata;

    public function type(): string
    {
        return 'custom-code';
    }

    public function label(): string
    {
        return 'Custom code';
    }

    public function category(): string
    {
        return 'advanced';
    }

    public function view(): string
    {
        return 'builder.blocks.custom-code';
    }

    public function defaults(): array
    {
        return ['content' => ['html' => '<p>Custom content</p>', 'css' => '', 'javascript' => ''], 'settings' => ['execute' => false], 'styles' => []];
    }

    public function allowedParents(): array
    {
        return ['section', 'container', 'stack', 'grid', 'columns', 'card'];
    }

    public function allowedChildren(): array
    {
        return [];
    }

    public function rules(): array
    {
        return ['content.html' => ['nullable', 'string', 'max:50000'], 'content.css' => ['nullable', 'string', 'max:50000'], 'content.javascript' => ['nullable', 'string', 'max:50000'], 'settings.execute' => ['boolean']];
    }

    public function editorSchema(): array
    {
        return [
            'content.html' => ['control' => 'code', 'label' => 'HTML', 'language' => 'html'],
            'content.css' => ['control' => 'code', 'label' => 'Scoped CSS', 'language' => 'css'],
            'content.javascript' => ['control' => 'code', 'label' => 'JavaScript', 'language' => 'javascript'],
            'settings.execute' => ['control' => 'checkbox', 'label' => 'Execute JavaScript'],
        ];
    }

    public function capabilities(): array
    {
        return ['duplicate', 'hide', 'custom-code'];
    }
}
