<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

use App\Users_Address;
use App\User;
use App\Vehicles;
use App\Vehicle_Address;

use Config;
use Validator;

class AddressController extends Controller {

	public function __construct (Vehicles $vehicle, User $user, Users_Address $user_address, Response $response, Vehicle_Address $vehicle_address) {
		$this->vehicle = $vehicle;
		$this->user = $user;
		$this->user_address = $user_address;
		$this->response = $response;
		$this->vehicle_address = $vehicle_address;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($id)
	{
		//
		$address = array(
			'street' => Input::get('street'),
			'city' => Input::get('city'),
			'state' => Input::get('state'),
			'zipcode' => Input::get('zipcode'),
		);

		$rules = array(
			'street' => 'required',
			'city' => 'required',
			'state' => 'required',
			'zipcode' => 'required',
		);

		$validator = Validator::make($address, $rules);

		$response = [];

		if ($validator->fails()) {
			$statusCode = 401;

			$response['Error'][] = [
				'message' => 'Error in your submission. Please try again.',
			];
		} else {
			$statusCode = 201;

			$address = new Users_Address($address);
			$geo = $address['street'] . ', ' . $address['city'] . ', ' . $address['state'] . ', ' . $address['zipcode'];
			$user = User::find($id);

			$coordinates = $this->geocode($geo);

			$user->latitude = $coordinates->lat;
			$user->longitude = $coordinates->lng;
			$user->save();

			$address = $user->address()->save($address);

			$response['address'] = [$address];
		}

		return $this->response->setContent($response, $statusCode);

	}

	/**
	 * Geocode user and vehicle address.
	 *
	 * @param  array  $address
	 * @return Response
	 */
	public function geocode($address) {
		$url = Config::get('services.map.url');
		$key = Config::get('services.map.key');

		$coordinates = str_replace(' ', '+', $url . $address . '&key=' . $key);

		$json = file_get_contents($coordinates);
		$map = json_decode($json);

		return $map->results[0]->geometry->location;
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  string $username
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
		$response = [];
		$input = Input::all();

		// Remove Access Token
		unset($input['access_token']);

		if (!empty($input)) {
			$statusCode = 201;
			$address = $this->user_address->where('user_id', '=', $id)->update($input);

			$user = $this->user->find($id);
			$address = $this->user_address->find($address);
			$geo = $address['street'] . ', ' . $address['city'] . ', ' . $address['state'] . ', ' . $address['zipcode']; 
			$coordinates = $this->geocode($geo);

			$user->latitude = $coordinates->lat;
			$user->longitude = $coordinates->lng;
			$user->save();

			$response['Message'] = ['Address updated.'];
			$response['user'] = $this->user->with('address', 'vehicles')->where('id', '=', $id)->first();
		} else {
			$statusCode = 401;
			$response['Error'] = ['Please enter data to update your Address.'];
			$response['user'] = $this->user->find($id)->with('address', 'vehicles');
		}

		return $this->response->setContent($response, $statusCode);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function vehicle_address($id, $vehicle_id) {
		$address = array(
			'street' => Input::get('street'),
			'city' => Input::get('city'),
			'state' => Input::get('state'),
			'zipcode' => Input::get('zipcode'),
		);

		$rules = array(
			'street' => 'required',
			'city' => 'required',
			'state' => 'required',
			'zipcode' => 'required',
		);

		$validator = Validator::make($address, $rules);

		$response = [];

		if ($validator->fails()) {

			$message = $validator->messages();
			$statusCode = 401;

			$response['Error'][] = [
				'message' => $message,
			];
		} else {
			$statusCode = 201;

			$address = new Vehicle_Address($address);
			$vehicle = $this->vehicle->find($vehicle_id);
			$address = $vehicle->vehicle_address()->save($address);

			$geo = $address['street'] . ', ' . $address['city'] . ', ' . $address['state'] . ', ' . $address['zipcode'];

			$coordinates = $this->geocode($geo);

			$vehicle->latitude = $coordinates->lat;
			$vehicle->longitude = $coordinates->lng;
			$vehicle->save();

			$response['address'] = [$this->vehicle->find($vehicle_id)->with('vehicle_address')->get()];
		}

		return $this->response->setContent($response, $statusCode);
	}

	public function update_vehicle_address($id, $vehicle_id) {
		//
		$response = [];
		$input = Input::all();

		// Remove Access Token
		unset($input['access_token']);

		if (!empty($input)) {
			$statusCode = 201;
			$address = $this->vehicle_address->where('vehicle_id', '=', $vehicle_id)->update($input);

			$vehicle = $this->vehicle->find($vehicle_id);
			$address = $this->vehicle_address->find($address);

			$geo = $address['street'] . ', ' . $address['city'] . ', ' . $address['state'] . ', ' . $address['zipcode']; 
			$coordinates = $this->geocode($geo);

			$vehicle->latitude = $coordinates->lat;
			$vehicle->longitude = $coordinates->lng;
			$vehicle->save();

			$response['Message'] = ['Address updated.'];
			$response['vehicle'] = $this->vehicle->where('id', '=', $vehicle_id)->with('vehicle_address')->first();
		} else {
			$statusCode = 401;
			$response['Error'] = ['Please enter data to update your vehicle address.'];
			$response['user'] = $this->vehicle->find($vehicle_id)->with('vehicle_address')->get();
		}

		return $this->response->setContent($response, $statusCode);
	}

}
