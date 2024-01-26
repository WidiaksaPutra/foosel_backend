<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Transaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->string('transactions_id');
            $table->string('users_email_pembeli');
            $table->string('users_email_penjual');
            $table->string('products_id');
            $table->bigInteger('category_id');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->double('total');
            $table->double('total_price');
            $table->double('shipping_price');
            $table->integer('quantity');
            $table->enum('status',['rejected', 'pending', 'success'])->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction');
    }
}
