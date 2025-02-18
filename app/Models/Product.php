<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'product_name',
        'description',
        'image',
        'category_id',

        'brand',
        'sku',
        'attributes',
        'expiry_date',
        'weight',

        'price',
        'stock_quantity',
        'minimum_purchase_qty',
        'wholesale_prices',
      
        'status',
        'rejection_reason',
        'price_updated_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:2',
        'stock_quantity' => 'integer',
        'minimum_purchase_qty' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity');
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributors::class, 'distributor_id');
    }
}
