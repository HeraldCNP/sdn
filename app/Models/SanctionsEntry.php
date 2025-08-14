<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SanctionsEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'sanctions_entry_id',
        'profile_id',
        'entry_event_type_id',
        'legal_basis_id',
        'sanctions_type_id',
        'comment',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
