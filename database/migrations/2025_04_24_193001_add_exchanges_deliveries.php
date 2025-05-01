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
        Schema::table('deliveries', function (Blueprint $table) {
            $table->unsignedBigInteger('exchange_for_return_id')->nullable()->after('status');
            $table->boolean('is_exchange_delivery')->default(false)->after('status');
            $table->foreign('exchange_for_return_id')->references('id')->on('return_requests')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropForeign(['exchange_for_return_id']);
            $table->dropColumn(['exchange_for_return_id', 'is_exchange_delivery']);
        });
    }
};