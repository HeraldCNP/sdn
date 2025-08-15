<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AliasType extends Model
{
    protected $table = 'alias_types';
    protected $primaryKey = 'ID';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['ID', 'Description'];
}
