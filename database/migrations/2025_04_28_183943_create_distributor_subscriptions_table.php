<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('distributor_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_id');
            $table->string('plan'); // 3_months, 6_months, 1_year
            $table->decimal('amount', 10, 2);
            $table->string('payment_id')->nullable(); // Paymongo payment ID
            $table->string('checkout_id')->nullable(); // Paymongo checkout ID
            $table->string('reference_number')->unique(); // Unique reference for tracking
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ['pending', 'active', 'expired', 'failed'])->default('pending');
            $table->timestamps();
            
            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('distributor_subscriptions');
    }
};