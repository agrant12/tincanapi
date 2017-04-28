<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

use App\User;
use App\Vehicles;
use App\Vehicle_Address;
use App\Vehicle_Photos;
use Validator;
use Storage;

class VehicleController extends Controller {


	/**
	 * Create a new filter instance.
	 *
	 * @param  Response  $response
	 * @return void
	 */
	public function __construct(Response $response, Vehicles $vehicle, User $user, Vehicle_Photos $vehicle_photos, Vehicle_Address $vehicle_address)
	{
		$this->user = $user;
		$this->vehicle = $vehicle;
		$this->response = $response;
		$this->vehicle_photos = $vehicle_photos;
		$this->vehicle_address = $vehicle_address;
	}

	/**
	 * Display all vehicle's.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		$json = [];

		try {
			$statusCode = 200;
			$response = $this->vehicle->with('vehicle_address', 'vehicle_photos')->paginate(20);
		} catch (Exception $e) {

			$response['Error'][] = [
				'message' => $e,
			];

			$statusCode = 404;
		}

		return $this->response->setContent($response, $statusCode);
	}

	/**
	 * Store a newly created vehicle in database.
	 *
	 * @return Response
	 */
	public function store($id)
	{
		$vehicle_info = array(
			'model' => Input::get('model'),
			'type' => Input::get('type'),
			'year' => Input::get('year'),
			'length' => Input::get('length'),
			'description' => Input::get('description'),
			'daily_rate' => Input::get('daily_rate'),
		);

		$rules = array(
			'model' => 'required',
			'type' => 'required',
			'year' => 'required',
			'length' => 'required',
			'description' => 'required',
			'daily_rate' => 'required',
		);

		$validator = Validator::make($vehicle_info, $rules);

		if ($validator->fails()) {
			$message = $validator->messages();

			$statusCode = 401;

			$response['Error'] = [
				$message
			];

		} else {
			$response = [];
			$statusCode = 201;

			$vehicle = new Vehicles($vehicle_info);
			$user = $this->user->find($id);

			$vehicle = $user->vehicle()->save($vehicle);

			$response['vehicle'] = [$vehicle];
		}
		
		return $this->response->setContent($response, $statusCode);
	}

	/**
	 * Upload vehicle Photos.
	 *
	 * @param  int  $vehicle_id
	 * @return Response
	 */

	public function vehicle_photos($user_id, $vehicle_id) {
		$vehicle_photos = array(
			'images' => Input::file('images')
		);

		$vehicle = $this->vehicle->find($vehicle_id);

		if ($vehicle_photos['images']) {

			$s3 = Storage::disk('s3');

			$upload_count = 0;
			foreach ($vehicle_photos['images'] as $key => $image) {
				$imageName =  'vehicle_' . $vehicle->id . '_' . $upload_count . '.' .
					$image->getClientOriginalExtension();
					
				$s3->put('vehicle_photos/' . $imageName, file_get_contents($image));
				
				$photo = new Vehicle_Photos;
				$photo->url = 'https://s3.amazonaws.com/tincanvehicle/vehicle_photos/' . $imageName;
				$photo->image_name = $imageName;
				$photo->vehicle_id = $vehicle->id;
				$photo->save();

				$upload_count++;
			}
		}
	}

	/**
	 *
	 * @param int $vehicle_id
	 * @return Response
	 *
	**/

	public function options($vehicle_id) {
		$vehicle_options = array(
			'options' => Input::get('options')
		);

		$json = array();
		$response = array();

		$vehicle = $this->vehicle->find($vehicle_id);

		if ($vehicle_options['options']) {
			$statusCode = 201;
			$options = $vehicle_options['options'];
			foreach ($options as $key => $option) {
				if (!empty($option)) {
					$json[] = $option;
				}
			}
			if (!empty($json)) {
				$options = json_encode($json);
				$vehicle->option = $options;
				$vehicle->save();
			}

			$response['vehicle'] = $vehicle->with('vehicle_address', 'vehicle_photos')->find($vehicle_id);
		}

		return $this->response->setContent($response, $statusCode);
	}

	/**
	 * Delete vehicle Photos.
	 *
	 * @param  int  $vehicle_id
	 * @return Response
	 */

	public function delete_vehicle_photo($id) {
		$photo = $this->vehicle_photos;
		$s3 = Storage::disk('s3');

		$photo = $photo::find($id);
		$imageName = $photo->image_name;

		$s3->delete('vehicle_photos/' . $imageName);
		$photo->delete();

		return 'Photo deleted!';
	}


	/**
	 * Return vehicle object that matches the ID
	 *
	 * @param  int  $vehicle_id
	 * @return Response
	 */
	public function show($id)
	{
		$json = [];
		$json = $this->vehicle->with('vehicle_address', 'vehicle_photos')->find($id);
		
		if (!empty($json)) {
			$statusCode = 200;
		} else {
			$statusCode = 400;
			$json['Error'][] = 'vehicle does not exist!';
		}

		return $this->response->setContent($json, $statusCode);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, $vehicle_id)
	{
		//
		$response = [];
		$input = Input::all();

		// Remove Access Token
		unset($input['access_token']);

		if (!empty($input)) {
			$statusCode = 201;
			$vehicle = $this->vehicle->find($vehicle_id)->update($input);

			$response['Message'] = ['vehicle updated'];
			$response['user'] = $this->user->where('id', '=', $id)->with('address', 'vehicle')->first();
		} else {
			$statusCode = 401;
			$response['Error'] = [
				'Please enter data to update your vehicle.',
			];
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
		$response = [];
		$vehicle = $this->vehicle->find($id);
		$vehicle->delete();

		return 'Vehicle Deleted';
	}

	/**
	 * Vehicle Rental Logic
	 *
	 * @param int $id
	 * @return Response
	 */
	public function rental($vehicle_id, $renter_id, $rentee_id)
	{
		$vehicle = $this->vehicle->find($vehicle_id);
		$renter = $this->user->find($renter_id);
		$rentee = $this->user->find($rentee_id);

		

		return $this->response->setContent($response, $statusCode);
	}

}
