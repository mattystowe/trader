<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Position;



class Activemarket extends Model
{



    public function account() {
      return $this->belongsTo('App\Account');
    }

    public function market() {
      return $this->belongsTo('App\Market');
    }



    //Does this active market have any open positions
    //return bool
    public function hasOpenPositions() {
      $number = Position::where([
        ['activemarket_id','=',$this->id],
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
