<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Config;
use App\User;
use App\RV;
use Mail;

class CreditCardController extends Controller {

	public function __construct(User $user, RV $rv) {
		$this->user = $user;
		$this->rv = $rv;
	}

	/** 
	 * Subscribe User to a rental plan
	 *
	 * @param int $id
	 * @return Response
	 */

	public function cancel_plan($id) {
		$user->subscription()->cancel();
	}

	/** 
	 * Charge User credit card
	 *
	 * @param int $id
	 * @param int $charge
	 * @return Response
	 */

	public function charge($id, $rv_id, $charge) {
		$creditCardToken = Input::get('stripeToken');

		$user = User::find($id);

		$user->charge($charge, [
			'source' => $creditCardToken,
		]);
		
		$this->receipt($id, $rv_id);
	}

	public function receipt($id, $rv_id) {
		//$data = array('code' => $confirmation_code, 'user' => $user->firstname);
		$user = $this->user->find($id);
		
		$email = $user->email;
		$username = $user->username;

		$data = array('email'=>$email, 'username' => $username);

		Mail::send('emails.receipt', $data, function($message) {
			$message->from('alving.nyc@gmail.com', 'Alvin Grant');
			$message->to('info@alvingrant.com', 'Alvin')
			->subject('Your RV Rental receipt');
		});

		return 'Transaction complete. Please check you email at ' . $email . ' for your receipt.';
	}


	public function subscribe($id) {
		$statusCode = 200;
		$creditCardToken = Input::get('stripeToken');
		$user = User::find($id);

		$user->subscription('2345')->create($creditCardToken, [
			'email'=> $user->email, 'description' => 'Dealer Subscription',
		]);

		return $this->response->setContent('User Subscribed', $statusCode);
	}
}
