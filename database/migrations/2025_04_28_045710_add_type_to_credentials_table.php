<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToCredentialsTable extends Migration
{
    public function up()
    {
        Schema::table('credentials', function (Blueprint $table) {
            $table->string('type')->nullable()->after('file_path'); // Add 'type' column
        });
    }

    public function down()
    {
        Schema::table('credentials', function (Blueprint $table) {
            $table->dropColumn('type'); // Rollback the 'type' column
        });
    }
}