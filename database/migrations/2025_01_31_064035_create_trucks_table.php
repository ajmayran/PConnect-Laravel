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
        Schema::create('trucks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_id');
            $table->string('plate_number')->unique();
            $table->boolean('is_ready_to_deliver')->default(false);
            $table->enum('status', ['available', 'on_delivery', 'maintenance'])->default('available');
            $table->timestamps();
            
            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('cascade');
        });
        Schema::create('truck_delivery', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('truck_id');
            $table->unsignedBigInteger('delivery_id');
            $table->timestamp('started_at')->nullable();
            $table->timestamps();

            $table->foreign('truck_id')->references('id')->on('trucks')->onDelete('cascade');
            $table->foreign('delivery_id')->references('id')->on('deliveries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('truck_delivery');
        Schema::dropIfExists('trucks');
    }
};
