<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alias extends Model
{
    use HasFactory;

    protected $fillable = [
        'identity_id',
        'alias_id',
        'name_part_value',
        'alias_type_id',
        'is_primary',
        'low_quality',
    ];

    public function identity(): BelongsTo
    {
        return $this->belongsTo(Identity::class);
    }
}
