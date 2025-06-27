<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher_Item extends Model {

	protected $table = 'voucher_items';

	public function category(){
		return $this->belongsTo('App\Category');
	}


	public function secondApproval(){
		return $this->category()->where('approval_steps', '=', 2);
	}

	public function voucher(){
		return $this->belongsTo('App\Voucher');
	}

	public function travelOrder(){
	    return $this->belongsTo('App\Voucher','travel_orderid');
    }
}
