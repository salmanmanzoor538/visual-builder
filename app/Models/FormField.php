<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    use HasUlids;

    protected $fillable = ['form_id', 'name', 'label', 'type', 'required', 'options', 'position'];

    protected function casts(): array
    {
        return ['required' => 'boolean', 'options' => 'array'];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(BuilderForm::class, 'form_id');
    }
}
