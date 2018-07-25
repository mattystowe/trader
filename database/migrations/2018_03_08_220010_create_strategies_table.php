<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStrategiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('strategies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('classinstance');
            $table->string('timeframe');
            $table->string('timeframereferences')->nullable();
            $table->timestamps();
        });


        DB::table('strategies')->insert([
          [
            'name'=>'LittleRinse V1',
            'description'=>'Little Rinse V1',
            'classinstance'=>'LittleRinseV1',
            'timeframe'=>'5m',
            'timeframereferences'=>'2h'
          ]
        ]);

        DB::table('strategies')->insert([
          [
            'name'=>'BigRinse V1',
            'description'=>'Big Rinse V1',
            'classinstance'=>'RinseMachineV1',
            'timeframe'=>'30m',
            'timeframereferences'=>'1d'
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
        Schema::dropIfExists('strategies');
    }
}
