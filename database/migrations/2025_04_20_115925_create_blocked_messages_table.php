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
        if (!Schema::hasTable('blocked_messages')) {
            Schema::create('blocked_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('distributor_id');
                $table->unsignedBigInteger('retailer_id');
                $table->text('reason')->nullable();
                $table->timestamps();
                
                $table->foreign('distributor_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('retailer_id')->references('id')->on('users')->onDelete('cascade');
                
                // Ensure a distributor can only block a retailer once
                $table->unique(['distributor_id', 'retailer_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_messages');
    }
};