<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Mess_Booking extends Model {

	protected $table = 'mess_bookings';
	protected $primaryKey = 'id';

	public function employee(){
        return $this->belongsTo('App\Employee');
    }

    public function messBookingTypes()
    {
        return $this->belongsTo(Mess_Booking_Type::class, 'id');
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approver_emp_number', 'employee_number');
    }
}
