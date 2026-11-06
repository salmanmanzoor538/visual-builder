<?php

namespace App\Builder\Blocks;

use App\Builder\Blocks\Concerns\HasDefaultBlockMetadata;
use App\Builder\Contracts\BlockDefinition;

final class ConfiguredBlock implements BlockDefinition
{
    use HasDefaultBlockMetadata;

    /** @param array<string, mixed> $defaults @param list<string> $parents @param list<string> $children @param array<string, mixed> $rules */
    public function __construct(
        private string $type,
        private string $label,
        private string $category,
        private string $view,
        private array $defaults,
        private array $parents,
        private array $children = [],
        private array $rules = [],
        private array $schema = [],
        private array $features = [],
    ) {}

    public function type(): string
    {
        return $this->type;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function category(): string
    {
        return $this->category;
    }

    public function view(): string
    {
        return $this->view;
    }

    public function defaults(): array
    {
        return $this->defaults;
    }

    public function allowedParents(): array
    {
        return $this->parents;
    }

    public function allowedChildren(): array
    {
        return $this->children;
    }

    public function rules(): array
    {
        return $this->rules;
    }

    public function editorSchema(): array
    {
        return $this->schema + $this->defaultEditorSchema();
    }

    public function capabilities(): array
    {
        return array_values(array_unique([...$this->defaultCapabilities(), ...$this->features]));
    }
}
