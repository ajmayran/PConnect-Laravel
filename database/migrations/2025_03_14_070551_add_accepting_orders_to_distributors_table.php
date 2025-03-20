<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->boolean('accepting_orders')->default(true);
        });
    }

    public function down()
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->dropColumn('accepting_orders');
        });
    }
};
