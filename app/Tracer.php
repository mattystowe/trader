<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tracer extends Model
{
  protected $fillable = [
    'market_id',
    'strategy_id',
    'utctimestamp',
    'timestamp_datetime',
    'message',
    'open',
    'high',
    'low',
    'close',
    'volume',
    'custom1',
    'custom2',
    'custom3',
    'custom4',
    'custom5',
    'custom6',
    'custom7',
    'custom8',
    'custom9',
    'custom10'
  ];

}
