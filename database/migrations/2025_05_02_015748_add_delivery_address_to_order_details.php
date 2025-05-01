<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryAddressToOrderDetails extends Migration
{
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->text('delivery_address')->nullable()->after('applied_discount');
        });
    }

    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('delivery_address');
        });
    }
}