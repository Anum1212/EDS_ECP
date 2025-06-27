<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model {


    public function businessUnit(){
        return $this->belongsTo('App\Business_Unit');
    }

	public function employees(){
        return $this->hasMany('App\Employee');
    }

    public function vouchers(){
        return $this->hasManyThrough('App\Voucher', 'App\Employee');
    }
}
