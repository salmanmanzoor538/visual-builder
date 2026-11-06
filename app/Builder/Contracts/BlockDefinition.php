<?php

namespace App\Builder\Contracts;

interface BlockDefinition
{
    public function type(): string;

    public function label(): string;

    public function category(): string;

    public function view(): string;

    public function defaults(): array;

    public function allowedParents(): array;

    public function allowedChildren(): array;

    public function rules(): array;

    /** @return array<string, array<string, mixed>> */
    public function editorSchema(): array;

    /** @return list<string> */
    public function capabilities(): array;

    public function schemaVersion(): int;
}
