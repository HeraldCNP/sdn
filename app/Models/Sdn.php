<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sdn extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'sdn_name',
        'sdn_type',
        'program',
        'remarks',
    ];

    /**
     * Get the aliases for the sdn.
     */
    public function aliases(): HasMany
    {
        return $this->hasMany(SdnAlias::class);
    }

    /**
     * Get the addresses for the sdn.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(SdnAddress::class);
    }
}
