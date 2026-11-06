<?php

namespace App\Builder\Blocks;

use App\Builder\Blocks\Concerns\HasDefaultBlockMetadata;
use App\Builder\Contracts\BlockDefinition;

final class HeadingBlock implements BlockDefinition
{
    use HasDefaultBlockMetadata;

    public function type(): string
    {
        return 'heading';
    }

    public function label(): string
    {
        return 'Heading';
    }

    public function category(): string
    {
        return 'content';
    }

    public function view(): string
    {
        return 'builder.blocks.heading';
    }

    public function defaults(): array
    {
        return ['content' => ['text' => 'Your headline'], 'settings' => ['level' => 2], 'styles' => ['fontSize' => 40, 'fontWeight' => 700]];
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
        return ['content.text' => ['required', 'string', 'max:500'], 'settings.level' => ['required', 'integer', 'between:1,6']];
    }
}
