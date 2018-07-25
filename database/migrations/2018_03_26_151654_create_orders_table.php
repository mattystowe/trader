<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('position_id');
            $table->string('status');
            $table->bigInteger('utctimestamp')->nullable();
            $table->dateTime('timestamp_datetime')->nullable();
            $table->string('type')->nullable();
            $table->string('side')->nullable();
            $table->float('price', 10,0)->nullable();
            $table->float('amount', 10,0)->nullable();
            $table->float('cost', 10,0)->nullable();
            $table->float('filled', 10,0)->nullable();
            $table->float('remaining', 10,0)->nullable();
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
        Schema::dropIfExists('orders');
    }
}
