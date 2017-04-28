<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Contracts\Billable as BillableContract;

class User extends Model implements BillableContract, AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword, Billable;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	protected $dates = ['trial_ends_at', 'subscription_ends_at'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['longitude', 'latitude', 'email', 'password', 'firstname', 'lastname', 'middlename', 'gender', 'suffix', 'remember_token', 'username', 'rv', 'stripe_active', 'stripe_id', 'stripe_subscription', 'stripe_plan', 'last_four', 'trial_ends_at', 'subscription_ends_at', 'access_token', 'confirmation_code'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'confirmation_code', 'remember_token', 'created_at', 'updated_at', 'stripe_active', 'stripe_id', 'stripe_subscription', 'stripe_plan', 'last_four', 'trial_ends_at', 'subscription_ends_at'];

	public function rv() {
		return $this->hasMany('App\User_RV', 'user_id');
	}

	public function address() {
		return $this->hasOne('App\Address', 'user_id');
	}

}
