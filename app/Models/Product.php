<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'description',
        'price',
        'stock_quantity',
        'minimum_purchase_qty',
        'category_id',
        'image',
        'distributor_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributors::class, 'distributor_id');
    }
}
