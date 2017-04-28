<?php namespace App\Http\Middleware;

use Closure;
use App\User;

class TokenOwner {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$user = User::find($request->user_id);
		$access_token = $request->access_token;
		
		if ($user->access_token !== $access_token) {
			return response('Unauthorized User', 401);
		}

		return $next($request);
	}

}
