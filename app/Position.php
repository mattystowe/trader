<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{

  protected $fillable = [
    'activestrategy_id',
    'activemarket_id',
    'entry_price',
    'exit_price',
    'status',
    'stoploss',
    'stoplimit',
    'entry_time',
    'exit_time',
    'order_cost'
  ];



  public function orders() {
    return $this->hasMany('\App\Order');
  }

  public function activemarket() {
    return $this->belongsTo('\App\Activemarket');
  }

  public function activestrategy() {
    return $this->belongsTo('\App\Activestrategy');
  }

  public function journals() {
    return $this->hasMany('\App\Journal');
  }


}
