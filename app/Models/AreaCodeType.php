<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaCodeType extends Model
{
    protected $table = 'area_code_types';
    protected $primaryKey = 'ID';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['ID', 'Description'];
}
