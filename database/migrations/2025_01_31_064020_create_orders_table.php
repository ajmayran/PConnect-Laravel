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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('retailer_id');
            $table->unsignedBigInteger('distributor_id');
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])
                ->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])
                ->default('pending');
            $table->text('landmark')->nullable();
            $table->timestamps();

            $table->foreign('retailer_id')->references('id')->on('retailers')->onDelete('cascade');
            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('cascade');
        });
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();
            
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_details');
    }
};
