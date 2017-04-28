<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class RV_Photos extends Model {

	//
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'rv_photos';

	protected $fillable = ['id', 'url', 'rv_id'];

	protected $hidden = ['created_at', 'updated_at', 'id'];

	public function rv() {
		return $this->belongsTo('App\RV');
	}

}
