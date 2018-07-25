<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchanges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('link');
            $table->string('classinstance');
            $table->float('fee', 10, 0)->nullable();
            $table->timestamps();
        });

        DB::table('exchanges')->insert([
          [
            'name'=>'Binance',
            'link'=>'https://www.binance.com/',
            'classinstance'=>'binance',
            'fee' => 0.0005
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
        Schema::dropIfExists('exchanges');
    }
}
