<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NamePartType extends Model
{
    protected $fillable = [
        'ID',
        'Description'
    ];

    protected $primaryKey = 'ID';
    public $incrementing = false;
    public $timestamps = false;
}
