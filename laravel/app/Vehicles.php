<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicles extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'vehicles';

	protected $fillable = ['id', 'longitude', 'latitude', 'class', 'model', 'features', 'year', 'user_id', 'length', 'details', 'daily_rate', 'available', 'features', 'type', 'description'];

	protected $hidden = ['created_at', 'updated_at'];

	public function users() {
		return $this->belongsTo('App\Users');
	}

	public function vehicle_address() {
		return $this->hasOne('App\Vehicle_Address', 'vehicle_id');
	}

	public function vehicle_photos() {
		return $this->hasMany('App\Vehicle_Photos', 'vehicle_id');
	}
}
