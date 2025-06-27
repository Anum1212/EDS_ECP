<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Mess_Booking_Type extends Model {

	protected $table = 'mess_booking_types';
	protected $primaryKey = 'id';

	public function MessBookings(){
        return $this->hasMany(Mess_Booking::class, 'id');
    }

}
