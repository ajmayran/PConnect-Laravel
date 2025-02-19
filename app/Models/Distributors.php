<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Distributors extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'company_email',
        'company_address',
        'company_phone_number',
        'bir_form',
        'sec_document',
        'profile_completed',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}