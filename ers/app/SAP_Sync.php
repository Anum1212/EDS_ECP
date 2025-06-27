<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class SAP_Sync extends Model {

	protected $table = 'sap_sync';

	public function employee()
	{
		return $this->belongsTo('App\Employee', 'employee_number', 'employee_number');
	}

	public function line_manager(){
		return $this->belongsTo('App\SAP_Sync', 'line_manager_id', 'employee_number');		
	}

	public function department(){
		return $this->belongsTo('App\Department', 'department');
	}

	public function businessUnit(){
		return $this->belongsTo('App\Business_Unit', 'business_unit');
	}

	public function entitlements(){
		return $this->hasMany('App\entitlements', 'employee_number', 'employee_number');
	}
}
