<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()//ini dijalankan untuk menambahkan atribut pada tabel
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('roles')->after('email')->default('PEMBELI');
            $table->string('phone')->after('email')->nullable();
            $table->string('username')->after('email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()//ini dijalankan untuk menghapus tabel
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('roles');
            $table->dropColumn('phone');
            $table->dropColumn('username');
        });
    }
}
