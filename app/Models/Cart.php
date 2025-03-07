<?php

namespace App\Models;

use App\Http\Controllers\Admin\Distributor;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'distributor_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(CartDetail::class);
    }

    public function calculateTotal()
    {
        return $this->details->sum('subtotal');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id');
    }
}
