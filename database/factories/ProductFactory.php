<?php

namespace Database\Factories;
use App\Models\Product;
use App\Models\Category;
use App\Http\Controllers\Admin\Distributor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{

    protected $model = Product::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'distributor_id' => 2, // Make sure this distributor exists
            'product_name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'stock_quantity' => $this->faker->numberBetween(0, 1000),
            'minimum_purchase_qty' => $this->faker->numberBetween(1, 10),
            'category_id' => $this->faker->numberBetween(1, Category::count()),
            'image' => 'img/default-product.jpg',
        ];
    }
}
