<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Reviews extends Model {

	//
	protected $table = 'user_reviews';

	protected $fillable = ['id', 'review', 'rating', 'reviewer', 'reviewee'];

	public function users() {
		return $this->belongsTo('App\Users');
	}

}
