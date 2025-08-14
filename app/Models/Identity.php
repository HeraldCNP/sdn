<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Identity extends Model
{
    use HasFactory;

    protected $fillable = [
        'identity_id',
        'profile_id',
        'is_primary',
        'is_false',
        'fixed_ref',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(Alias::class);
    }
}
