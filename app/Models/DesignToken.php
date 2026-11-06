<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignToken extends Model
{
    use HasUlids;

    protected $fillable = ['website_id', 'group', 'name', 'value', 'version'];

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }
}
