<?php

namespace App\Builder\Blocks\Concerns;

trait HasDefaultBlockMetadata
{
    public function editorSchema(): array
    {
        return $this->defaultEditorSchema();
    }

    protected function defaultEditorSchema(): array
    {
        return [
            'styles.color' => ['control' => 'color', 'label' => 'Text color', 'responsive' => true],
            'styles.backgroundColor' => ['control' => 'color', 'label' => 'Background', 'responsive' => true],
            'styles.fontSize' => ['control' => 'number', 'label' => 'Font size', 'min' => 8, 'max' => 160, 'responsive' => true],
            'styles.paddingTop' => ['control' => 'number', 'label' => 'Top padding', 'min' => 0, 'max' => 400, 'responsive' => true],
            'styles.paddingBottom' => ['control' => 'number', 'label' => 'Bottom padding', 'min' => 0, 'max' => 400, 'responsive' => true],
        ];
    }

    public function capabilities(): array
    {
        return $this->defaultCapabilities();
    }

    protected function defaultCapabilities(): array
    {
        return ['duplicate', 'hide', 'responsive', 'styles'];
    }

    public function schemaVersion(): int
    {
        return 1;
    }
}
