<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('username');
            $table->string('email');
            $table->string('password');
            $table->string('remember_token')->nullable();
            $table->timestamps();
        });

        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->string('account_name')->nullable();
            $table->string('current_balance');
            $table->timestamps();
            $table->foreign('customer_id')->references('id')->on('users');
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->string('balance');
            $table->string('description');
            $table->string('check_path_file');
            $table->boolean('approved')->default(false);
            $table->timestamp('created_at');
            $table->foreign('account_id')->references('id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('transactions');
    }
}
