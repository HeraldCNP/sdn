<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alias extends Model
{
    protected $table = 'aliases';
    protected $primaryKey = 'ID';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['ID', 'FixedRef', 'AliasTypeID', 'Primary_', 'LowQuality', 'DocumentedNameID'];
    protected $casts = [
        'Primary_' => 'boolean',
        'LowQuality' => 'boolean',
    ];

    public function identity(): BelongsTo
    {
        return $this->belongsTo(Identity::class, 'FixedRef', 'FixedRef');
    }

    public function aliasType(): BelongsTo
    {
        return $this->belongsTo(AliasType::class, 'AliasTypeID');
    }

    public function documentedName(): BelongsTo
    {
        return $this->belongsTo(DocumentedName::class, 'DocumentedNameID');
    }
}
