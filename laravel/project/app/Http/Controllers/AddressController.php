<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

use App\Address;
use App\User;
use App\RV;
use App\RV_Address;

use Config;
use Validator;

class AddressController extends Controller {

	public function __construct (RV $rv, User $user, Address $address, Response $response, RV_Address $rv_address) {
		$this->rv = $rv;
		$this->user = $user;
		$this->address = $address;
		$this->response = $response;
		$this->rv_address = $rv_address;
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

			$address = new Address($address);
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
	 * Geocode user and rv address.
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
			$address = $this->address->where('user_id', '=', $id)->update($input);

			$user = $this->user->find($id);
			$address = $this->address->find($address);
			$geo = $address['street'] . ', ' . $address['city'] . ', ' . $address['state'] . ', ' . $address['zipcode']; 
			$coordinates = $this->geocode($geo);

			$user->latitude = $coordinates->lat;
			$user->longitude = $coordinates->lng;
			$user->save();

			$response['Message'] = ['Address updated.'];
			$response['user'] = $this->user->where('id', '=', $id)->with('address', 'rv')->first();
		} else {
			$statusCode = 401;
			$response['Error'] = ['Please enter data to update your Address.'];
			$response['user'] = $this->user->find($id)->with('address', 'rv');
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

	public function rv_address($id) {
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

			$address = new RV_Address($address);
			$rv = $this->rv->find($id);
			$address = $rv->rv_address()->save($address);

			$geo = $address['street'] . ', ' . $address['city'] . ', ' . $address['state'] . ', ' . $address['zipcode'];

			$coordinates = $this->geocode($geo);

			$rv->latitude = $coordinates->lat;
			$rv->longitude = $coordinates->lng;
			$rv->save();

			$response['address'] = [$this->rv->find($id)->with('rv_address')->get()];
		}

		return $this->response->setContent($response, $statusCode);
	}

	public function update_rv_address($id) {
		//
		$response = [];
		$input = Input::all();

		// Remove Access Token
		unset($input['access_token']);

		if (!empty($input)) {
			$statusCode = 201;
			$address = $this->rv_address->where('rv_id', '=', $id)->update($input);

			$rv = $this->rv->find($id);
			$address = $this->rv_address->find($address);

			$geo = $address['street'] . ', ' . $address['city'] . ', ' . $address['state'] . ', ' . $address['zipcode']; 
			$coordinates = $this->geocode($geo);

			$rv->latitude = $coordinates->lat;
			$rv->longitude = $coordinates->lng;
			$rv->save();

			$response['Message'] = ['Address updated.'];
			$response['rv'] = $this->rv->where('id', '=', $id)->with('rv_address')->first();
		} else {
			$statusCode = 401;
			$response['Error'] = ['Please enter data to update your Address.'];
			$response['user'] = $this->rv->find($id)->with('rv_address')->get();
		}

		return $this->response->setContent($response, $statusCode);
	}

}
