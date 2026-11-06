<?php

namespace App\Builder\Blocks;

use App\Builder\Blocks\Concerns\HasDefaultBlockMetadata;
use App\Builder\Contracts\BlockDefinition;

final class ButtonBlock implements BlockDefinition
{
    use HasDefaultBlockMetadata;

    public function type(): string
    {
        return 'button';
    }

    public function label(): string
    {
        return 'Button';
    }

    public function category(): string
    {
        return 'content';
    }

    public function view(): string
    {
        return 'builder.blocks.button';
    }

    public function defaults(): array
    {
        return ['content' => ['label' => 'Get started', 'url' => '#'], 'settings' => ['target' => '_self'], 'styles' => []];
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
        return ['content.label' => ['required', 'string', 'max:120'], 'content.url' => ['required', 'string', 'max:2048'], 'settings.target' => ['nullable', 'in:_self,_blank']];
    }
}
