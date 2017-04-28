<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use App\Vehicles;
use Illuminate\Support\Facades\Input;

class SearchController extends Controller {
	
	public function __construct(Response $response, Vehicles $vehicle, User $user) {
		$this->response = $response;
		$this->vehicle = $vehicle;
		$this->user = $user;
	}

	/**
	* Search vehicle's
	* @param $string
	* @return Response
	*/

	public function searchvehicle() {
		$model = Input::get('model');
		$type = Input::get('type');
		$length = Input::get('length');

		$response = [];

		if ($model && $type && $length) {
			$vehicle = $this->vehicle->whereModelAndTypeAndLength($model, $type, $length)->get();
		} else if ($model && $type && empty($length)) {
			$vehicle = $this->vehicle->whereModelAndType($model, $type)->get();
		} else if (empty($model) && $type && $length) {
			$vehicle = $this->vehicle->whereTypeAndLength($type, $length)->get();
		} else if (empty($model) && empty($type) && $length) {
			$vehicle = $this->vehicle->where('length', '=', $length)->get();
		} else if ($model && empty($type) && $length) {
			$vehicle = $this->vehicle->whereModelAndLength($model, $type, $length)->get();
		} else if (empty($model) && empty($type) && empty($length)) {
			$vehicle = 'Please enter a search query';
		}
		
		$statusCode = 200;
		$response = $vehicle;

		return $this->response->setContent($response, $statusCode);
	}

	public function searchUser() {
		$username = Input::get('username');
		$type = Input::get('type');

		$response = [];

		if ($username && $type) {
			$user = $this->user->whereUsernameandType($username, $type)->get();
		} else if ($username && empty($type)) {
			$user = $this->user->where('username', '=', $username)->get();
		} else if (empty($username) && $type) {
			$user = $this->user->where('type', '=', $type)->get();
		}

		$statusCode = 200;
		$response = $user;

		return $this->response->setContent($response, $statusCode);
	}
}
