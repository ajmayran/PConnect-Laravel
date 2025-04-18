<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_id');
            // Basic Information
            $table->string('product_name');
            $table->text('description');
            $table->string('image')->nullable();
            $table->unsignedBigInteger('category_id');

            // Specifications
            $table->string('brand')->nullable();
            $table->string('sku')->unique()->nullable();
            $table->json('attributes')->nullable(); // For dynamic attributes
            $table->json('tags')->nullable();
            $table->decimal('weight', 8, 2)->nullable();


            // Sales Information
            $table->decimal('price', 10, 2);
            // $table->integer('stock_quantity');
            $table->integer('minimum_purchase_qty');
            $table->json('wholesale_prices')->nullable(); // For bulk pricing

            // Status
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->string('rejection_reason')->nullable();
            $table->timestamp('price_updated_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
