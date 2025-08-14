<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SdnAlias extends Model
{
    use HasFactory;

    protected $fillable = [
        'sdn_id',
        'alias_type',
        'last_name',
        'first_name',
        'whole_name',
    ];

    /**
     * Get the sdn that owns the alias.
     */
    public function sdn(): BelongsTo
    {
        return $this->belongsTo(Sdn::class);
    }
}
