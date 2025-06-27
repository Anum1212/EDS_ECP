<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model {

	public function rateGrade(){
	    return $this->belongsTo('App\Grade', 'grade_id');
    }

}
