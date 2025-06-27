<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model {

	public function employees(){
        return $this->belongsToMany('App\Employee');
    }

    public function activeBanks(){
        return $this->where('active', '=', '1');
    }

}
