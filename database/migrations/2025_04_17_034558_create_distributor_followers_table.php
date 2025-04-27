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
        Schema::create('distributor_followers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_id');
            $table->unsignedBigInteger('retailer_id');
            $table->timestamps();
            
            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('cascade');
            $table->foreign('retailer_id')->references('id')->on('users')->onDelete('cascade');
            
            // Ensure a retailer can only follow a distributor once
            $table->unique(['distributor_id', 'retailer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_followers');
    }
};