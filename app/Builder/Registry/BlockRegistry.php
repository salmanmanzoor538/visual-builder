<?php

namespace App\Builder\Registry;

use App\Builder\Contracts\BlockDefinition;
use InvalidArgumentException;

final class BlockRegistry
{
    /** @var array<string, BlockDefinition> */
    private array $blocks = [];

    public function register(BlockDefinition $block): self
    {
        if (isset($this->blocks[$block->type()])) {
            throw new InvalidArgumentException("Builder block [{$block->type()}] is already registered.");
        }

        $this->blocks[$block->type()] = $block;

        return $this;
    }

    public function get(string $type): BlockDefinition
    {
        return $this->blocks[$type] ?? throw new InvalidArgumentException("Unknown builder block [{$type}].");
    }

    /** @return array<string, BlockDefinition> */
    public function all(): array
    {
        return $this->blocks;
    }
}
