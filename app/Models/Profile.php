<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'party_sub_type_id',
    ];

    public function identities(): HasMany
    {
        return $this->hasMany(Identity::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(Feature::class);
    }

    public function sanctionsEntries(): HasMany
    {
        return $this->hasMany(SanctionsEntry::class);
    }
}
