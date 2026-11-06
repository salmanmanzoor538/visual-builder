<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Website extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = ['workspace_id', 'created_by', 'name', 'slug', 'status', 'settings'];

    protected function casts(): array
    {
        return ['settings' => 'array', 'published_at' => 'immutable_datetime'];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class)->orderBy('position');
    }

    public function publishVersions(): HasMany
    {
        return $this->hasMany(PublishVersion::class);
    }

    public function activePublishVersion(): BelongsTo
    {
        return $this->belongsTo(PublishVersion::class, 'active_publish_version_id');
    }

    public function designTokens(): HasMany
    {
        return $this->hasMany(DesignToken::class);
    }

    public function cmsCollections(): HasMany
    {
        return $this->hasMany(CmsCollection::class);
    }

    public function forms(): HasMany
    {
        return $this->hasMany(BuilderForm::class);
    }

    public function globalSections(): HasMany
    {
        return $this->hasMany(GlobalSection::class);
    }
}
