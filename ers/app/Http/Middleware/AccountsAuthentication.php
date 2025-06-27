<?php namespace App\Http\Middleware;

use App\Employee;
use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class AccountsAuthentication {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        $data['employee'] = Employee::find(Session::get('id'));
        if(isset($data['employee']->custom_view) && $data['employee']->custom_view != ''){
            $allowed = [0, 1, 3];
            if(in_array($data['employee']->account_type, $allowed)){
                return $next($request);
            }
            else{
                Session::flash('error', 'You do not have permission to perform this action.');
                return Redirect::back();
            }
        }
        else{
            Session::flash('error', 'You do not have permission to perform this action.');
            return Redirect::back();
        }
	}

}
