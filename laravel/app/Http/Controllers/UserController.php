<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Authorizer;

use App\User;
use App\Users_Address;
use App\Reviews;
use Validator;

class UserController extends Controller {

	/**
	 * Create a new filter instance.
	 *
	 * @param  Response  $response
	 * @return void
	 */
	public function __construct(Response $response, User $user, Request $request, Users_Address $users_address, Reviews $review)
	{
		$this->user = $user;
		$this->review = $review;
		$this->request = $request;
		$this->response = $response;
		$this->users_address = $users_address;
	}

	/**
	 * Display a all users with Vehicle.
	 *
	 * @return Response
	 */
	public function index()
	{

		$response = [];
		
		$user = $this->user->with('address', 'vehicle', 'review')->paginate(20);

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
		$user = $this->user->with('address', 'vehicle', 'review')->find($id);
		
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

			$user_updated = $this->user->with('address', 'vehicle')->find($id);
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

	/**
	 * Allow Users to provide feedback for renters and rentees
	 *
	 * @param int $id
	 * @return Response
	 */
	public function review($reviewer_id, $reviewee_id)
	{
		$reviewer = $this->user->find($reviewer_id);
		$reviewee = $this->user->find($reviewee_id);
		
		$review_field = array(
			'review' => Input::get('review'),
			'rating' => Input::get('rating'),
			'reviewer' => $reviewer->id,
			'reviewee' => $reviewee->id
		);

		$rules = array(
			'review' => 'required',
			'rating' => 'required'
		);

		$validator = Validator::make($review_field, $rules);

		if ($validator->fails()) {
			$message = $validator->messages();
			$statusCode = 401;

			$response['Error'][] = [
				'message' => $message,
			];
	
		} else {
			$review = new Reviews($review_field);
			$review_final = $this->user->review()->save($review);

			$r = $this->review->where('reviewee', $reviewee_id)->get();
			
			$ratings = array();
			foreach ($r as $key => $review) {
				$ratings[] = $review->rating;

			}
			
			$avg = array_sum($ratings) / count($ratings);

			$reviewee->rating = $avg;
			$reviewee->save();
			
			$statusCode = 201;
			$response['review'] = $review_final;
		}

		return $this->response->setContent($response, $statusCode);
	}
}
