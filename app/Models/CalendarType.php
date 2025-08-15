<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarType extends Model
{
    protected $table = 'calendar_types';
    protected $primaryKey = 'ID';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['ID', 'Description'];
}
