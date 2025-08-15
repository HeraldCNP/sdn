<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NamePartGroup extends Model
{
    protected $table = 'name_part_groups';
    protected $primaryKey = 'ID';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['ID', 'NamePartTypeID', 'IdentityID'];

    public function identity(): BelongsTo
    {
        return $this->belongsTo(Identity::class, 'IdentityID');
    }
}
