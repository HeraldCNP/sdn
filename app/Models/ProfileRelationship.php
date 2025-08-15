<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileRelationship extends Model
{
    protected $table = 'profile_relationships';
    protected $primaryKey = 'ID';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['ID', 'FromProfileID', 'ToProfileID', 'RelationTypeID', 'RelationQualityID', 'Former', 'SanctionsEntryID', 'Comment'];
    protected $casts = [
        'Former' => 'boolean',
    ];

    public function fromProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'FromProfileID');
    }

    public function toProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'ToProfileID');
    }
}
