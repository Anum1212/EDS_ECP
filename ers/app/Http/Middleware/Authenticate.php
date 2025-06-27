<?php namespace App\Http\Middleware;

use App\Employee;
use App\Voucher;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Session;

class Authenticate {

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

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
		if(isset($data['employee']->nick_name)){
		    $customLayout = '';
		    $inProcessVouchers = 0;
		    $inProcessTravelOrders = 0;
		    $documentsTransitVouchers = 0;
		    if(isset($data['employee']->custom_view) && $data['employee']->custom_view != ''){
                $customViews = explode(',',$data['employee']->custom_view);
                foreach ($customViews as $customView){
                    $name = 'custom-'.$customView.'.layout.ers-layout';
                    if(View()->exists($name)){
                        $request->attributes->set('custom_view', $customView);
                        $customLayout = 'custom-'.$customView.'.layout.ers-layout';
                        break;
                    }
                }
            }
		    \Illuminate\Support\Facades\View::share('customLayout', $customLayout);
            return $next($request);
		}
		else{
			return redirect('welcome');
		}
	}

}
