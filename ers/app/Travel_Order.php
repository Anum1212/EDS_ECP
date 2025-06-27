<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Travel_Order extends Model {

	protected $table = 'travel_orders';

    public function employee()
    {
        return $this->belongsTo('App\Employee');
    }

    public function travelOrderStatus(){
        return $this->belongsToMany('App\Employee', 'travel_order_employee', 'travel_order_id')->withPivot('approved', 'created_at', 'updated_at');
    }

    public function currentApprover(){
        return $this->belongsToMany('App\Employee','travel_order_employee', 'travel_order_id')->wherePivot('approved', '=', NULL);
    }

    public function approvers(){
        return $this->belongsToMany('App\Employee','travel_order_employee', 'travel_order_id')->withPivot('comments');
    }
}
