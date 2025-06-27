<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {

	public function voucherItems(){
        return $this->hasMany('App\Voucher_Item');
    }

    public function glMappings(){
        return $this->hasMany('App\GL_Mapping');
    }
    public function company(){
	    return $this->belongsTo('App\Company');
    }


}
