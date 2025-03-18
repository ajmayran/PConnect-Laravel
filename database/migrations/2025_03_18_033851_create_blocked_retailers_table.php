<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blocked_retailers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_id');
            $table->unsignedBigInteger('retailer_id');
            $table->text('reason')->nullable();
            $table->timestamps();
            
            $table->foreign('distributor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('retailer_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unique(['distributor_id', 'retailer_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('blocked_retailers');
    }
};