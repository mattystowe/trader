<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    public function accounts() {
      return $this->hasMany('App\Account');
    }

    public function markets() {
      return $this->hasMany('App\Market');
    }

}
