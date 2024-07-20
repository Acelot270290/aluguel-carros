<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressAndImageFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('img')->nullable()->after('remember_token');
            $table->string('zip_code')->nullable()->after('img');
            $table->string('street')->nullable()->after('zip_code');
            $table->string('number')->nullable()->after('street');
            $table->string('city')->nullable()->after('number');
            $table->string('neighborhood')->nullable()->after('city');
            $table->string('state')->nullable()->after('neighborhood');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('img');
            $table->dropColumn('zip_code');
            $table->dropColumn('street');
            $table->dropColumn('number');
            $table->dropColumn('city');
            $table->dropColumn('neighborhood');
            $table->dropColumn('state');
        });
    }
}
