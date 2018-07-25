<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activestrategy_id');
            $table->integer('activemarket_id');
            $table->float('entry_price', 10, 0)->nullable();
            $table->float('exit_price', 10, 0)->nullable();
            $table->dateTime('entry_time')->nullable();
            $table->dateTime('exit_time')->nullable();
            $table->string('status');
            $table->float('stoploss',10,0)->nullable();
            $table->float('stoplimit',10,0)->nullable();
            $table->float('trade',10,0)->nullable(); // r2r - quote currency amount traded
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
        Schema::dropIfExists('positions');
    }
}
