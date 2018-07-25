<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exchange_id');
            $table->string('symbol');
            $table->string('symbolid');
            $table->string('base_symbol');
            $table->string('quote_symbol');
            $table->float('min_amount', 10, 0);
            $table->float('min_price', 10, 0);
            $table->float('min_cost', 10, 0);
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
        Schema::dropIfExists('markets');
    }
}
