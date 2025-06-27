<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model {

	public function businessUnits(){
        return $this->hasMany('App\Business_Unit');
    }

}
