<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class RV_Address extends Model {

	//
	//
	protected $table = 'rv_address';

	protected $fillable = ['rv_id', 'street', 'city', 'state', 'zipcode'];

	protected $hidden = ['updated_at', 'created_at', 'user_id'];

	public function rv() {
		return $this->hasOne('App\RV', 'rv_id');
	}

}
