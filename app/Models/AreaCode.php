<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AreaCode extends Model
{
    protected $table = 'area_codes';
    protected $primaryKey = 'ID';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['ID', 'CountryID', 'AreaCodeTypeID', 'Description', 'Code'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'CountryID');
    }

    public function areaCodeType(): BelongsTo
    {
        return $this->belongsTo(AreaCodeType::class, 'AreaCodeTypeID');
    }
}
