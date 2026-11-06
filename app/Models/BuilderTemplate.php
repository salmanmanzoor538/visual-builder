<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuilderTemplate extends Model
{
    use HasUlids;

    protected $fillable = ['workspace_id', 'created_by', 'key', 'name', 'category', 'scope', 'thumbnail', 'schema_version', 'snapshot', 'is_builtin'];

    protected function casts(): array
    {
        return ['snapshot' => 'array', 'is_builtin' => 'boolean'];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
