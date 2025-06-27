<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model {

	public function company(){
	    return $this->belongsTo('App\Company');
    }

}
