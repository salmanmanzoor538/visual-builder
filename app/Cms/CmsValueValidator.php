<?php

namespace App\Cms;

use App\Models\CmsCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class CmsValueValidator
{
    private const TYPES = ['text', 'rich_text', 'number', 'boolean', 'date', 'datetime', 'color', 'url', 'email', 'media', 'option', 'multi_option', 'location', 'group', 'repeater'];

    /** @return list<string> */
    public function types(): array
    {
        return self::TYPES;
    }

    /** @param array<string, mixed> $values @return array<string, mixed> */
    public function validate(CmsCollection $collection, array $values): array
    {
        $rules = [];
        foreach ($collection->fields as $field) {
            $fieldRules = [$field->is_required ? 'required' : 'nullable'];
            $fieldRules = [...$fieldRules, ...match ($field->type) {
                'number' => ['numeric'], 'boolean' => ['boolean'], 'date' => ['date_format:Y-m-d'], 'datetime' => ['date'],
                'color' => ['regex:/^#[0-9a-fA-F]{6}$/'], 'url' => ['url:http,https'], 'email' => ['email'],
                'multi_option', 'group', 'repeater', 'location' => ['array'],
                'option' => [Rule::in($field->configuration['options'] ?? [])],
                default => ['string', 'max:'.($field->type === 'rich_text' ? 100000 : 10000)],
            }];
            $rules[$field->key] = $fieldRules;
        }

        return Validator::make($values, $rules)->validate();
    }
}
