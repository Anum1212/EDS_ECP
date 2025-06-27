<?php namespace App\Http\Middleware;

use Closure;

class HttpsProtocol {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$ip_address = ['127.0.0.1', '172.16.1.88', '172.16.1.46', '172.16.1.47', '172.16.1.48', '172.16.1.146'];

		if(!$request->secure()){
			if(!in_array($request->getClientIp(), $ip_address)){
				return redirect()->secure($request->getRequestUri());
            }
        }
        return $next($request);

    }

}
