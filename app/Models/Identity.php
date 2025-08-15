<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Identity extends Model
{
    protected $table = 'identities';
    protected $primaryKey = 'ID';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['ID', 'FixedRef', 'Primary_', 'False_', 'ProfileID'];
    protected $casts = [
        'Primary_' => 'boolean',
        'False_' => 'boolean',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'ProfileID');
    }

    public function documentedNames(): HasMany
    {
        return $this->hasMany(DocumentedName::class, 'FixedRef', 'FixedRef');
    }

    public function namePartGroups(): HasMany
    {
        return $this->hasMany(NamePartGroup::class, 'IdentityID');
    }
}
