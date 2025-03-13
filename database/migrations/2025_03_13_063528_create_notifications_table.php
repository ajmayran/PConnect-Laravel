<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'order_status', 'delivery_update', etc.
            $table->json('data'); // Store all relevant data
            $table->boolean('is_read')->default(false);
            $table->unsignedBigInteger('related_id')->nullable(); // Order ID, Delivery ID, etc.
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};