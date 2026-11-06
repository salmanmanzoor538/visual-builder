<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmission extends Model
{
    use HasUlids;

    protected $fillable = ['form_id', 'payload', 'ip_hash', 'user_agent', 'consented_at'];

    protected function casts(): array
    {
        return ['payload' => 'encrypted:array', 'consented_at' => 'immutable_datetime'];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(BuilderForm::class, 'form_id');
    }
}
