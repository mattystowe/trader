<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
  protected $fillable = [
    'account_id',
    'symbol',
    'free',
    'used',
    'total'
  ];

}
