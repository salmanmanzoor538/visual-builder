<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PublishVersion extends Model
{
    use HasUlids;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['manifest' => 'array', 'published_at' => 'immutable_datetime'];
    }

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(PublishedPage::class);
    }
}
