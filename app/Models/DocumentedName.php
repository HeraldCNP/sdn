<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentedName extends Model
{
    protected $table = 'documented_names';
    protected $primaryKey = 'ID';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['ID', 'FixedRef', 'DocNameStatusID', 'AliasID'];

    public function identity(): BelongsTo
    {
        return $this->belongsTo(Identity::class, 'FixedRef', 'FixedRef');
    }

    public function alias(): BelongsTo
    {
        return $this->belongsTo(Alias::class, 'AliasID');
    }
}
