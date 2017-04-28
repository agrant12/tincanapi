<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use App\RV;
use App\Address;
use Illuminate\Support\Facades\Input;
use Authorizer;

class UserController extends Controller {

	/**
	 * Create a new filter instance.
	 *
	 * @param  Response  $response
	 * @return void
	 */
	public function __construct(Response $response, User $user, Request $request, Address $address)
	{
		$this->response = $response;
		$this->user = $user;
		$this->request = $request;
		$this->address = $address;
	}

	/**
	 * Display a all users with RV.
	 *
	 * @return Response
	 */
	public function index()
	{

		$response = [];
		
		$user = $this->user->with('address', 'rv')->paginate(20);

		if (!empty($user)) {
			$statusCode = 200;
			$response = $user; 
		} else {
			$statusCode = 404;
			$response['Error'] = [
				'message' => 'No users found.',
			];
		}

		return $this->response->setContent($response, $statusCode);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
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
		$user = $this->user->with('address', 'rv')->find($id);
		
		if (!empty($user)) {
			$statusCode = 200;
			$response['user'] = [
				$user,
			];
		} else {
			$statusCode = 404;
			$response['Error'] = [
				'User does not exist!',
			];
		}

		return $this->response->setContent($response, $statusCode);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  string  $username
	 * @return Response
	 */
	public function edit(User $user, $username)
	{

	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  string  $username
	 * @return Response
	 */
	public function update($id)
	{
		//
		$inputs = Input::all();
		$address = array();
		
		if (!empty($inputs)) {
			$statusCode = 201;
			$user = $this->user->find($id)->update($inputs);

			foreach ($inputs as $key => $input) {
				if ($key == 'street' || 'city' || 'state' || 'zipcode') {
					$address = array( $key => $input );
					$address = $this->address->where('user_id', '=', $id)->update($address);
				}
			}

			$user_updated = $this->user->with('address', 'rv')->find($id);
			$response['Message'] = ['User updated.'];
			$response['User'] = [$user_updated];
		} else {
			$statusCode = 401;
			$response['Error'] = [
				'Please update fill out form to update your information.'
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
