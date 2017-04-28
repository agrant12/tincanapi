<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Users_Address extends Model {

	//
	protected $table = 'users_address';

	protected $fillable = ['user_id', 'street', 'city', 'state', 'zipcode'];

	protected $hidden = ['updated_at', 'created_at', 'user_id'];

	public function users() {
		return $this->belongsTo('App\Users');
	}
}
