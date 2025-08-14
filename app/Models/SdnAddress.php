<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SdnAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'sdn_id',
        'address_line1',
        'address_line2',
        'address_line3',
        'city',
        'state',
        'postal_code',
        'country',
    ];

    /**
     * Get the sdn that owns the address.
     */
    public function sdn(): BelongsTo
    {
        return $this->belongsTo(Sdn::class);
    }
}
