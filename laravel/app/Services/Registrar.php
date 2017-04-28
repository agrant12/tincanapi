<?php namespace App\Services;

use App\User;
use Validator;
use Illuminate\Contracts\Auth\Registrar as RegistrarContract;

class Registrar implements RegistrarContract {

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	public function validator(array $data)
	{
		return Validator::make($data, [
			'firstname' => 'required|max:255',
			'lastname' => 'required|max:255',
			'username' => 'required|max:255|unique:users',
			'middlename' => 'max:255',
			'suffix' => 'max:255',
			'gender' => 'max:255',
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|confirmed|min:6',
		]);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return User
	 */
	public function create(array $data)
	{
		return User::create([
			'firstname' => $data['firstname'],
			'lastname' => $data['lastname'],
			'username' => $data['username'],
			'middlename' => $data['middlename'],
			'suffix' => $data['suffix'],
			'gender' => $data['gender'],
			'email' => $data['email'],
			'password' => bcrypt($data['password']),
		]);
	}

}
