<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = ['website_id', 'name', 'slug', 'position', 'is_homepage', 'is_visible', 'is_indexable', 'include_in_sitemap', 'seo_title', 'meta_description', 'canonical_url', 'social_image', 'settings'];

    protected function casts(): array
    {
        return ['settings' => 'array', 'is_homepage' => 'boolean', 'is_visible' => 'boolean', 'is_indexable' => 'boolean', 'include_in_sitemap' => 'boolean'];
    }

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    public function nodes(): HasMany
    {
        return $this->hasMany(BuilderNode::class)->orderBy('position');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PageVersion::class);
    }
}
