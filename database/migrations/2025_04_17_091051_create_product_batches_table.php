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
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('batch_number');
            $table->integer('quantity');
            $table->date('manufacturing_date')->nullable();
            $table->date('expiry_date');
            // $table->decimal('cost_price', 10, 2)->nullable(); // Cost price for this specific batch
            $table->string('supplier')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('received_at');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};