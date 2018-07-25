<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Position;

class Activestrategy extends Model
{
    public function account() {
      return $this->belongsTo('App\Account');
    }

    public function strategy() {
      return $this->belongsTo('App\Strategy');
    }

    public function positions() {
      return $this->hasMany('App\Position');
    }


    //Does this active strategy have any open positions
    //return bool
    public function hasOpenPositions() {
      $number = Position::where([
        ['activestrategy_id','=',$this->id],
        ['status','=','OPEN']
      ])->get()->count();

      if ($number>0) {
        return true;
      }

      return false;

    }


    //return bool true if active = true or hasOpenPositons
    public function isActive() {
      if ($this->active or $this->hasOpenPositions()) {
        return true;
      } else {
        return false;
      }
    }

}
