<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class RV extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users_rv';

	protected $fillable = ['id', 'longitude', 'latitude', 'class', 'model', 'features', 'year', 'user_id', 'length', 'details', 'daily_rate', 'weekly_rate', 'available', 'features', 'type', 'description'];

	protected $hidden = ['created_at', 'updated_at'];

	public function users() {
		return $this->belongsTo('App\Users');
	}

	public function photos() {
		return $this->hasMany('App\RV_Photos', 'user_id');
	}

	public function rv_address() {
		return $this->hasOne('App\RV_Address', 'rv_id');
	}

	public function rv_photos() {
		return $this->hasMany('App\RV_Photos', 'rv_id');
	}
}
