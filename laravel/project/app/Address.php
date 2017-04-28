<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model {

	//
	protected $table = 'address';

	protected $fillable = ['user_id', 'street', 'city', 'state', 'zipcode'];

	protected $hidden = ['updated_at', 'created_at', 'user_id'];

	public function users() {
		return $this->belongsTo('App\Users');
	}
}
