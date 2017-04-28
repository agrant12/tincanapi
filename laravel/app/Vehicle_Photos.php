<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle_Photos extends Model {

	//
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'vehicle_photos';

	protected $fillable = ['id', 'url', 'vehicle_id'];

	protected $hidden = ['created_at', 'updated_at', 'id'];

	public function vehicle() {
		return $this->belongsTo('App\Vehicles');
	}

}
