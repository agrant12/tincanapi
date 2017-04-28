<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class User_RV extends Model {

	//
	protected $table = 'users_rv';

	protected $fillable = ['class', 'model', 'features', 'year'];

	protected $hidden = ['created_at', 'updated_at'];

	public function user() {
		return $this->hasOne('App\User', 'id');
	}

}
