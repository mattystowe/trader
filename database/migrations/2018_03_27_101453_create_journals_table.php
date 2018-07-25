<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('position_id');
            $table->string('journal_type'); // profit, loss, fee
            $table->float('profit', 10, 0)->nullable();
            $table->float('loss', 10, 0)->nullable();
            $table->float('fee', 10, 0)->nullable();
            $table->string('currency');
            $table->dateTime('posting_date')->nullable();
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
        Schema::dropIfExists('journals');
    }
}
