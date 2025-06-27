<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class BU_Head extends Model {

	protected $table = 'bu_head';

	public function employee()
	{
		return $this->belongsTo('App\SAP_Sync', 'employee_number', 'employee_number');
	}

}
