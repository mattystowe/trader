<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exchange_id');
            $table->string('name');
            $table->string('apikey');
            $table->string('secret');
            $table->timestamps();
        });

        DB::table('accounts')->insert([
          [
            'exchange_id'=>1,
            'name'=>'Binance Main',
            'apikey'=>'changeyourapikey',
            'secret'=>'changeyoursecret'
          ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
