<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class GL_Mapping extends Model {

    protected $table = 'gl_mappings';

	public function category(){
        return $this->belongsTo('App\Category');
    }

}
