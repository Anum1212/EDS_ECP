<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Business_Unit extends Model {

    protected $table = 'business_units';

    public function departments(){
        return $this->hasMany('App\Department', 'business_unit_id');
    }

    public function approvers(){
        return $this->belongsToMany('App\Employee', 'business_unit_employee', 'business_unit_id');
    }

	public function levelApprovers($level){
        return $this->approvers()->where('level', '>=', $level)->orderby('level', 'ASC');
    }
    
    public function divisionalApprover(){
        return $this->approvers()->where('level', '=', 4)->select('employee_number', 'employee_name', 'email')->orderby('level', 'ASC');
    }

    public function company(){
        return $this->belongsTo('App\Company');
    }

    public function siblings(){
        return $this->belongsToMany('App\Business_Unit', 'business_units_siblings', 'business_unit_id','sibling_id');
    }

    public function bu_head() {
        return $this->hasOne('App\BU_Head', 'bu_id', 'id');
    }
    
}
