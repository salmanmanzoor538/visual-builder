<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuilderNode extends Model
{
    use HasUlids;

    protected $fillable = ['page_id', 'parent_id', 'type', 'position', 'content', 'settings', 'styles', 'responsive', 'is_hidden'];

    protected function casts(): array
    {
        return ['content' => 'array', 'settings' => 'array', 'styles' => 'array', 'responsive' => 'array', 'is_hidden' => 'boolean'];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position');
    }
}
