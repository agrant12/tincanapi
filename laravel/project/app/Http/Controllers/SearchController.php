<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use App\RV;
use Illuminate\Support\Facades\Input;

class SearchController extends Controller {
	
	public function __construct(Response $response, RV $rv) {
		$this->response = $response;
		$this->rv = $rv;
	}

	/**
	* Search RV's
	* @param $string
	* @return Response
	*/

	public function searchRV(RV $rv) {
		$query = Input::get('query');
		$response = [];

		$rv = $this->rv->whereRaw("MATCH(model,type) AGAINST(? IN BOOLEAN MODE)", 
			array($query)
		)->paginate(20);

		$statusCode = 200;
		$response = $rv;

		return $this->response->setContent($response, $statusCode);
	}
}
