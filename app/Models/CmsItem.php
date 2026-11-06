<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsItem extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = ['cms_collection_id', 'created_by', 'slug', 'status', 'revision', 'values', 'published_at', 'scheduled_for'];

    protected function casts(): array
    {
        return ['values' => 'array', 'published_at' => 'immutable_datetime', 'scheduled_for' => 'immutable_datetime'];
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(CmsCollection::class, 'cms_collection_id');
    }
}
