<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class Distributors extends Model
{
    protected $fillable = ['user_id', 'company_profile_image', 'company_name', 'company_email', 'company_address', 'company_phone_number', 'approval_status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Additional business logic can be added here
}
