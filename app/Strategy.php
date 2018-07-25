<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Strategy extends Model
{
    public function activestrategies() {
      return $this->hasMany('App\Activestrategy');
    }
}
