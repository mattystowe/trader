<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
  protected $fillable = [
    'exchange_id',
    'symbol',
    'symbolid',
    'base_symbol',
    'quote_symbol',
    'min_amount',
    'min_price',
    'min_cost'
  ];


  public function exchange() {
    return $this->belongsTo('App\Exchange');
  }

  public function activemarkets() {
    return $this->hasMany('App\Activemarket');
  }


}
