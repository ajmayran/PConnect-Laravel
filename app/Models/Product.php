<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;


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
        'minimum_purchase_qty',
        'wholesale_prices',

        'status',
        'rejection_reason',
        'price_updated_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:2',
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

    public function batches()
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_product');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }

    // this method is for dynamically calculate stock based on batches
    public function getStockQuantityAttribute($value)
    {
        // For batch-managed categories, calculate from batches
        if ($this->isBatchManaged()) {
            return $this->batches()->sum('quantity');
        }

        // For regular products, calculate from stocks table
        $stockIn = $this->stocks()->where('type', 'in')->sum('quantity');
        $stockOut = $this->stocks()->where('type', 'out')->sum('quantity');

        return $stockIn - $stockOut;
    }

    public function isBatchManaged()
    {
        $batchCategories = [
            'Ready To Cook',
            'Beverages',
            'Instant Products',
            'Snacks',
            'Sauces & Condiments',
            'Juices & Concentrates',
            'Powdered Products',
            'Frozen Products',
            'Dairy Products'
        ];

        // Get category name through the relationship
        return $this->category && in_array($this->category->name, $batchCategories);
    }

    public function hasActiveDiscount()
    {
        return $this->discounts()
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->exists();
    }

    public function getActiveDiscountAttribute()
    {
        return $this->discounts()
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    public function getDisplayPriceAttribute()
    {
        $activeDiscount = $this->activeDiscount;

        if ($activeDiscount && $activeDiscount->type === 'percentage') {
            $discountAmount = $this->price * $activeDiscount->percentage / 100;
            return $this->price - $discountAmount;
        }

        return $this->price;
    }
}
