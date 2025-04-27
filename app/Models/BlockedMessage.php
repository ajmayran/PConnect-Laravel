<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'distributor_id',
        'retailer_id',
        'reason',
    ];

    /**
     * Get the distributor who blocked the retailer.
     */
    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributor_id');
    }

    /**
     * Get the retailer who was blocked.
     */
    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id');
    }
}