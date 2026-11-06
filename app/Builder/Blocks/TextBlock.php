<?php

namespace App\Builder\Blocks;

use App\Builder\Blocks\Concerns\HasDefaultBlockMetadata;
use App\Builder\Contracts\BlockDefinition;

final class TextBlock implements BlockDefinition
{
    use HasDefaultBlockMetadata;

    public function type(): string
    {
        return 'text';
    }

    public function label(): string
    {
        return 'Text';
    }

    public function category(): string
    {
        return 'content';
    }

    public function view(): string
    {
        return 'builder.blocks.text';
    }

    public function defaults(): array
    {
        return ['content' => ['text' => 'Add supporting copy here.'], 'settings' => [], 'styles' => ['fontSize' => 18]];
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
        return ['content.text' => ['required', 'string', 'max:10000']];
    }
}
