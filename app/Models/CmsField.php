<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsField extends Model
{
    use HasUlids;

    protected $fillable = ['cms_collection_id', 'name', 'key', 'type', 'position', 'is_required', 'is_unique', 'configuration'];

    protected function casts(): array
    {
        return ['is_required' => 'boolean', 'is_unique' => 'boolean', 'configuration' => 'array'];
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(CmsCollection::class, 'cms_collection_id');
    }
}
