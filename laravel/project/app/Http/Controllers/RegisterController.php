<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

use Validator;
use App\User;
use Auth;
use Authorizer;
use Mail;
use Session;
use Storage;

class RegisterController extends Controller {

	public function __construct(Response $response, User $user, Session $session) {
		$this->response = $response;
		$this->user = $user;
		$this->session = $session;
	}

	/**
	 * Generate access_token for user
	 *
	 */

	public function access_token($id) {
		
		$user = User::find($id);

		if (empty($user->access_token)) {
			$statusCode = 200;
			$access_token = Authorizer::issueAccessToken();

			$user->access_token = $access_token['access_token'];
			$user->save();
			$response['user'] = [$user]; 
		} else {
			$statusCode = 201;
			$response['Message'] = [
				'Token already exists for user.'
			]; 
			$response['user'] = [$user];
		}

		return $this->response->setContent($response, $statusCode);
	}

	/**
	 * Create new User.
	 *
	 * @return Response
	 */
	public function create(User $user)
	{
		$credentials = array(
			'firstname' => Input::get('firstname'),
			'lastname' => Input::get('lastname'),
			'middlename' => Input::get('middlename'),
			'suffix' => Input::get('suffix'),
			'username' => Input::get('username'),
			'email' => Input::get('email'),
			'password' => Input::get('password'),
			'gender' => Input::get('gender'),
			'type' => Input::get('type'),
			'rv' => Input::get('rv'),
			'image' => Input::file('image'),
		);

		$rules = array(
			'email' => 'unique:users,email',
			'username' => 'unique:users',
			'firstname' => 'required',
			'lastname' => 'required',
			'password' => 'required',
			'type' => 'required',
			'image' => 'mimes:jpeg,bmp,png',
		);

		$validator = Validator::make($credentials, $rules);

		$response = [];

		if ($validator->fails()) {
			$message = $validator->messages();
			$statusCode = 401;

			$response['Error'][] = [
				'message' => $message,
			];

		} else {

			$confirmation_code = str_random(30);

			$user->firstname = $credentials['firstname'];
			$user->lastname = $credentials['lastname'];
			$user->middlename = $credentials['middlename'];
			$user->suffix = $credentials['suffix'];
			$user->username = $credentials['username'];
			$user->email = $credentials['email'];
			$user->password = bcrypt($credentials['password']);
			$user->gender = $credentials['gender'];
			$user->type = $credentials['type'];
			$user->rv = $credentials['rv'];
			$user->confirmation_code = $confirmation_code;

			if (!empty($credentials['image'])) {
				$uploadedFile = $credentials['image'];

				$imageName = $user->username . '.' . $credentials['image']->getClientOriginalExtension();

				$s3 = Storage::disk('s3');
				$s3->put('profile_pictures/' . $imageName, file_get_contents($uploadedFile));

				$user->profile = 'https://s3.amazonaws.com/tincanrv/profile_pictures/' . $imageName;
			}

			$user->save();

			$statusCode = 201;

			$data = array('code' => $confirmation_code, 'user' => $user->firstname);
			
			/*Mail::send('emails.verify', $data, function($message) {
				$message->from('alving.nyc@gmail.com', 'Alvin Grant');
				$message->to(Input::get('email'), Input::get('username'))
				->subject('Verify your email address to fully access your account');
			});

			$response['Message'] = [
				'Please check your email at ' . $user->email . ' to verify your account.'
			];*/

			$response['user'] = [
				$user
			];
		}

		return $this->response->setContent($response, $statusCode);
	}

	/**
	 * Login User
	 *
	 * @return Response
	 */

	public function login() {

		$response = [];

		$credentials = array(
			'username' => Input::get('username'), 
			'password' => Input::get('password')
		);
		
		if (Auth::attempt( $credentials, true )) {
			$user = Auth::user();

			if ($user->confirmed == 0) {
				$response['Message'] = [
					'Please confirm your account at ' . $user->email . ' to fully access your account!!'
				];
			}

			$response['user'] = [
				$user,
			];

			$statusCode = 201;
		} else {

			$statusCode = 401;

			$response['Error'][] = [
				'message' => 'Invalid login credentials! Please try again.',
			];
		}

		return $this->response->setContent($response, $statusCode);

	}

	/**
	 * Logout User
	 *
	 */

	public function logout() {

	}

	/**
	 * Confirm user account after successful registration
	 *	@param $confirmation_code string
	 *  @return Response
	 */
	public function confirm($confirmation_code) {
		
		$response = [];
		$user = User::whereConfirmationCode($confirmation_code)->first();

		if (!$confirmation_code) {
			$statusCode = 400;
			$response['Error'] = ['Code not valid'];
		} else if (!$user) {
			$statusCode = 400;
			$response['Error'] = ['User does not exist'];
		} else {
			$user->confirmed = 1;
			$user->confirmation_code = null;
			$user->save();

			$statusCode = 200;
			$response['Message'] = ['You have successfully verified your account!'];
			$response['user'] = [$user];
		}

		return $this->response->setContent($response, $statusCode);
	}
}
