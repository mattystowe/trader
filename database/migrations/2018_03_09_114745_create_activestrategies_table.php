<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivestrategiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activestrategies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('strategy_id');
            $table->integer('account_id');
            $table->float('order_commitment_level', 10, 0)->nullable(); // float used as % of total base currency balance to place on single order.
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        DB::table('activestrategies')->insert([
          [
            'strategy_id'=>1,
            'account_id'=>1,
            'order_commitment_level'=>0.300,
            'active'=>true
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
        Schema::dropIfExists('activestrategies');
    }
}
