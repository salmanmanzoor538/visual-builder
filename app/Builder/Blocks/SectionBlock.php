<?php

namespace App\Builder\Blocks;

use App\Builder\Blocks\Concerns\HasDefaultBlockMetadata;
use App\Builder\Contracts\BlockDefinition;

final class SectionBlock implements BlockDefinition
{
    use HasDefaultBlockMetadata;

    public function type(): string
    {
        return 'section';
    }

    public function label(): string
    {
        return 'Section';
    }

    public function category(): string
    {
        return 'layout';
    }

    public function view(): string
    {
        return 'builder.blocks.section';
    }

    public function defaults(): array
    {
        return ['content' => [], 'settings' => ['tag' => 'section'], 'styles' => ['paddingTop' => 64, 'paddingBottom' => 64]];
    }

    public function allowedParents(): array
    {
        return ['root'];
    }

    public function allowedChildren(): array
    {
        return ['container', 'stack', 'grid', 'columns', 'card', 'heading', 'text', 'button', 'image', 'divider', 'spacer', 'quote', 'badge', 'navigation', 'logo', 'icon', 'video', 'gallery', 'list', 'testimonial', 'pricing', 'faq', 'social-links', 'embed', 'form', 'custom-code'];
    }

    public function rules(): array
    {
        return ['settings.tag' => ['nullable', 'in:section,header,footer,main,aside']];
    }
}
