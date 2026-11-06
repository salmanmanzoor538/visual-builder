<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsCollection extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = ['website_id', 'name', 'slug', 'status', 'schema_version'];

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(CmsField::class)->orderBy('position');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CmsItem::class);
    }
}
