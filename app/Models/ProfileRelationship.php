<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileRelationship extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_relationship_id',
        'from_profile_id',
        'to_profile_id',
        'relation_type_id',
        'relation_quality_id',
        'former',
        'sanctions_entry_id',
    ];
}
