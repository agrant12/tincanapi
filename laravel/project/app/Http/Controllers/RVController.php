<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

use App\User;
use App\RV;
use App\RV_Address;
use App\RV_Photos;
use Validator;
use Storage;

class RVController extends Controller {


	/**
	 * Create a new filter instance.
	 *
	 * @param  Response  $response
	 * @return void
	 */
	public function __construct(Response $response, RV $rv, User $user, RV_Photos $rv_photos, RV_Address $rv_address)
	{
		$this->user = $user;
		$this->rv = $rv;
		$this->response = $response;
		$this->rv_photos = $rv_photos;
		$this->rv_address = $rv_address;
	}

	/**
	 * Display all RV's.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		$json = [];

		try {
			$statusCode = 200;
			$response = $this->rv->with('rv_address', 'rv_photos')->get();
		} catch (Exception $e) {

			$response['Error'][] = [
				'message' => $e,
			];

			$statusCode = 404;
		}

		return $this->response->setContent($response, $statusCode);
	}

	/**
	 * Store a newly created RV in database.
	 *
	 * @return Response
	 */
	public function store($id)
	{
		$rv_info = array(
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

		$validator = Validator::make($rv_info, $rules);

		if ($validator->fails()) {
			$message = $validator->messages();

			$statusCode = 401;

			$response['Error'] = [
				$message
			];

		} else {
			$response = [];
			$statusCode = 201;

			$rv = new RV($rv_info);
			$user = $this->user->find($id);

			$rv = $user->rv()->save($rv);

			$response['rv'] = [$rv];
		}
		
		return $this->response->setContent($response, $statusCode);
	}

	/**
	 * Upload RV Photos.
	 *
	 * @param  int  $rv_id
	 * @return Response
	 */

	public function rv_photos($user_id, $rv_id) {
		$rv_photos = array(
			'images' => Input::file('images')
		);

		$rv = $this->rv->find($rv_id);

		if ($rv_photos['images']) {

			$s3 = Storage::disk('s3');

			$upload_count = 0;
			foreach ($rv_photos['images'] as $key => $image) {
				$imageName =  'RV_' . $rv->id . '_' . $upload_count . '.' .
					$image->getClientOriginalExtension();
					
				$s3->put('rv_photos/' . $imageName, file_get_contents($image));
				
				$photo = new RV_Photos;
				$photo->url = 'https://s3.amazonaws.com/tincanrv/rv_photos/' . $imageName;
				$photo->rv_id = $rv->id;
				$photo->save();

				$upload_count++;
			}
		}
	}

	/**
	 * Delete RV Photos.
	 *
	 * @param  int  $rv_id
	 * @return Response
	 */

	public function delete_rv_photo($rv_id) {

	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($rv_id)
	{
		$json = [];
		$json = $this->rv->with('rv_address')->find($rv_id);
		
		if (!empty($json)) {
			$statusCode = 200;
		} else {
			$statusCode = 400;
			$json['Error'][] = 'RV does not exist!';
		}

		return $this->response->setContent($json, $statusCode);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, $rv_id)
	{
		//
		$response = [];
		$input = Input::all();

		// Remove Access Token
		unset($input['access_token']);

		if (!empty($input)) {
			$statusCode = 201;
			$rv = $this->rv->find($rv_id)->update($input);

			$response['Message'] = ['RV updated'];
			$response['user'] = $this->user->where('id', '=', $id)->with('address', 'rv')->first();
		} else {
			$statusCode = 401;
			$response['Error'] = [
				'Please enter data to update your RV.',
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
	}

}
