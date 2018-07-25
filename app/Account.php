<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Balance;
use App\BalanceIngestor;

class Account extends Model
{
    public function exchange() {
      return $this->belongsTo('App\Exchange');
    }

    public function activemarkets() {
      return $this->hasMany('App\Activemarket');
    }

    public function activestrategies() {
      return $this->hasMany('App\Activestrategy');
    }





    public function loadRealTimeBalances() {
      $papertrade = env('PAPERTRADE');
      if (!$papertrade) {
        $balanceLoader = new BalanceIngestor;
        $balanceLoader->getBalances($this);
      }
    }


    public function getFreeBalance($symbol) {
      $balance = Balance::where([
        ['account_id','=',$this->id],
        ['symbol','=',$symbol]
      ])->get();
      if ($balance->isNotEmpty()) {
        $return = $balance->first();
        return $return->free;
      } else {
        return false;
      }


    }




    public function updateFreeBalance($symbol, $value) {
      $papertrade = env('PAPERTRADE');
      if ($papertrade) {
          $balance = Balance::where([
            ['account_id','=',$this->id],
            ['symbol','=',$symbol]
          ])->get();
          if ($balance->isNotEmpty()) {
            $this_balance = $balance->first();
            $this_balance->free = $value;
            if ($this_balance->save()) {
              return true;
            } else {
              return false;
            }
          } else {
            return false;
          }
      } else {
        return true;
      }
    }


}
