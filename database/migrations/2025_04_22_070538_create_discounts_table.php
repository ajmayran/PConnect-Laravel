<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->enum('type', ['percentage', 'freebie']);
            $table->decimal('percentage', 5, 2)->nullable(); // For percentage discounts
            $table->integer('buy_quantity')->nullable();     // For freebie discounts: buy X
            $table->integer('free_quantity')->nullable();    // For freebie discounts: get Y free
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('cascade');
        });

        // Pivot table for products that have discounts applied
        Schema::create('discount_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('discount_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();
            
            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('discount_product');
        Schema::dropIfExists('discounts');
    }
};