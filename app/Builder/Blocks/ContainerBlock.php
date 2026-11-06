<?php

namespace App\Builder\Blocks;

use App\Builder\Blocks\Concerns\HasDefaultBlockMetadata;
use App\Builder\Contracts\BlockDefinition;

final class ContainerBlock implements BlockDefinition
{
    use HasDefaultBlockMetadata;

    public function type(): string
    {
        return 'container';
    }

    public function label(): string
    {
        return 'Container';
    }

    public function category(): string
    {
        return 'layout';
    }

    public function view(): string
    {
        return 'builder.blocks.container';
    }

    public function defaults(): array
    {
        return ['content' => [], 'settings' => [], 'styles' => ['maxWidth' => 1200]];
    }

    public function allowedParents(): array
    {
        return ['section', 'container'];
    }

    public function allowedChildren(): array
    {
        return ['container', 'stack', 'grid', 'columns', 'card', 'heading', 'text', 'button', 'image', 'divider', 'spacer', 'quote', 'badge', 'navigation', 'logo', 'icon', 'video', 'gallery', 'list', 'testimonial', 'pricing', 'faq', 'social-links', 'embed', 'form', 'custom-code'];
    }

    public function rules(): array
    {
        return [];
    }
}
