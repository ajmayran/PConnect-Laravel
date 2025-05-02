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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('distributor_id');
            $table->enum('payment_status', ['pending','unpaid', 'paid', 'failed', 'partial'])->default('unpaid');
            // $table->unsignedBigInteger('collected_by')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('payment_note')->nullable();
            $table->timestamps();

            // $table->foreign('collected_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
