<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOhlcvsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ohlcvs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('market_id');
            $table->bigInteger('utctimestamp')->nullable();
            $table->dateTime('timestamp_datetime')->nullable();
            $table->float('open', 10, 0)->nullable();
      			$table->float('high', 10, 0)->nullable();
      			$table->float('low', 10, 0)->nullable();
      			$table->float('close', 10, 0)->nullable();
      			$table->float('volume', 10, 0)->nullable();
            $table->string('timeframe');
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
        Schema::dropIfExists('ohlcvs');
    }
}
