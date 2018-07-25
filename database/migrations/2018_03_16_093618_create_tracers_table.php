<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTracersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('market_id');
            $table->integer('strategy_id');
            $table->bigInteger('utctimestamp')->nullable();
            $table->dateTime('timestamp_datetime')->nullable();
            $table->string('message')->nullable();
            $table->float('open', 10, 0)->nullable();
      			$table->float('high', 10, 0)->nullable();
      			$table->float('low', 10, 0)->nullable();
      			$table->float('close', 10, 0)->nullable();
      			$table->float('volume', 10, 0)->nullable();
            $table->float('buy',10,0)->nullable();
            $table->float('sell',10,0)->nullable();
            $table->float('custom1', 10, 0)->nullable();
            $table->float('custom2', 10, 0)->nullable();
            $table->float('custom3', 10, 0)->nullable();
            $table->float('custom4', 10, 0)->nullable();
            $table->float('custom5', 10, 0)->nullable();
            $table->float('custom6', 10, 0)->nullable();
            $table->float('custom7', 10, 0)->nullable();
            $table->float('custom8', 10, 0)->nullable();
            $table->float('custom9', 10, 0)->nullable();
            $table->float('custom10', 10, 0)->nullable();
            $table->float('custom11', 10, 0)->nullable();
            $table->float('custom12', 10, 0)->nullable();
            $table->float('custom13', 10, 0)->nullable();
            $table->float('custom14', 10, 0)->nullable();
            $table->float('custom15', 10, 0)->nullable();
            $table->float('custom16', 10, 0)->nullable();
            $table->float('custom17', 10, 0)->nullable();
            $table->float('custom18', 10, 0)->nullable();
            $table->float('custom19', 10, 0)->nullable();
            $table->float('custom20', 10, 0)->nullable();
            $table->float('custom21', 10, 0)->nullable();
            $table->float('custom22', 10, 0)->nullable();
            $table->float('custom23', 10, 0)->nullable();
            $table->float('custom24', 10, 0)->nullable();
            $table->float('custom25', 10, 0)->nullable();
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
        Schema::dropIfExists('tracers');
    }
}
