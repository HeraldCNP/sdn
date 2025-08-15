<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends Model
{
    protected $table = 'profiles';
    protected $primaryKey = 'ID';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['ID', 'PartySubTypeID', 'FixedRef'];

    public function distinctParty(): BelongsTo
    {
        return $this->belongsTo(DistinctParty::class, 'FixedRef', 'FixedRef');
    }

    public function identities()

    {
        return $this->hasMany(Identity::class, 'ProfileID');
    }

    public function fromProfileRelationships(): HasMany
    {
        return $this->hasMany(ProfileRelationship::class, 'FromProfileID');
    }

    public function toProfileRelationships(): HasMany
    {
        return $this->hasMany(ProfileRelationship::class, 'ToProfileID');
    }
}
