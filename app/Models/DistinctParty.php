<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DistinctParty extends Model
{
    protected $table = 'distinct_parties';
    protected $primaryKey = 'FixedRef';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['FixedRef', 'Comment'];

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'FixedRef', 'FixedRef');
    }
}
