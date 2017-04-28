<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle_Address extends Model {

	//
	protected $table = 'vehicle_address';

	protected $fillable = ['vehicle_id', 'street', 'city', 'state', 'zipcode'];

	protected $hidden = ['updated_at', 'created_at', 'user_id'];

	public function vehicle() {
		return $this->hasOne('App\Vehicles', 'vehicle_id');
	}

}
