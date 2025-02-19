<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Distributors;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'product_name',
        'description',
        'price',
        'stock_quantity',
        'minimum_purchase_qty',
        'category_id',
        'image',
        'status',
        'rejection_reason'
    ];

    protected $casts = [
        'price' => 'decimal:2',
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
        return $this->belongsTo(Distributors::class, foreignKey: 'distributor_id');
    }
}
