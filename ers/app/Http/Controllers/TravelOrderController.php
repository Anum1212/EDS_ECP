<?php namespace App\Http\Controllers;

use App\Approval_Level;
use App\Business_Unit;
use App\Department;
use App\Employee;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Travel_Order;
use App\Travel_Order_Employee;
use App\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class TravelOrderController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$employee_id = Session::get('id');
		if(isset($employee_id)){
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$data['customView'] = 'custom-'.$data['employee']->custom_view.'.vouchers';

			if($data['employee']->account_type == 0 || $data['employee']->account_type == 1){
				$data['travelOrders'] = Travel_Order::all();
			}
			else{
				$data['travelOrders'] = $data['employee']->travelOrders;
			}
			return view('basic.travel-orders', $data);
		}
		else{
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function updateStatus(){
		$employee_id = Session::get('id');
		if(isset($employee_id)){
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$data['customView'] = 'custom-'.$data['employee']->custom_view.'.update-voucher-documents-status';
			$data['vouchers'] = Voucher::where('status', '=', 'Approved - Documents in transit')->where('is_travel_order', '=', 1)->get();
			return view('basic.update-travel-order-documents-status', $data);
		}
		else{
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$employee_id = Session::get('id');
		if(isset($employee_id)){
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$data['departments'] = Department::all();

			return view('basic.add-travel-order', $data);
		}
		else{
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$employee_id = Session::get('id');
		$filename = '';
		if(isset($employee_id)){
			$data['employee'] = Employee::find($employee_id);
			$dateRange = $request->input('date_range');
			DB::beginTransaction();
			$travelOrder = new Travel_Order();
			if(isset($dateRange)){
				$dateRange = explode('-', $dateRange);
				$travelOrder->date_from = date('Y-m-d H:i:s', strtotime($dateRange[0]));
				$travelOrder->date_to = date('Y-m-d H:i:s', strtotime($dateRange[1]));
			}
			$travelOrder->travel_order_type = $request->input('travel_order_type');
			$travelOrder->purpose= $request->input('purpose');
			$travelOrder->places_to_visit = $request->input('places_to_visit');
			$travelOrder->accomodation = $request->input('accomodation');
			$travelOrder->charge_to_cost_center = $request->input('cost_center');
			$travelOrder->order_number = $request->input('order_number');
			$travelOrder->status = 'Draft';
			if($request->input('travel_order_type') == 'Local'){
				$travelOrder->amount = $request->input('amount');
			}
			elseif($request->input('travel_order_type') == 'Foreign'){
				//Converting USD to PKR using Fixer API
				$url = 'http://data.fixer.io/api/'.date('Y-m-d', strtotime($travelOrder->date_from)).'?access_key=466ea18406c2ed1bbefbd39b783dd005&symbols=USD,PKR';
				//dump($url);
				$getForexRates = curl_init($url);
				curl_setopt($getForexRates, CURLOPT_RETURNTRANSFER, true);
				$result = json_decode(curl_exec($getForexRates));
				//dump($result);
				curl_close($getForexRates);
				$USD = $result->rates->PKR/$result->rates->USD;
				dump($USD);
				//Closing Conversion

				$travelOrder->forex_amount = $request->input('amount');
				$travelOrder->cur = 'USD';
				$travelOrder->amount = $request->input('amount')*$USD;

				//Getting and Storing the Travelling Bond
				$file = $request->file('bond');
				if(isset($file)){
					if($request->file('bond')->isValid()){
						if($request->file('bond')->getClientOriginalName()){
							$unique_name = md5($request->file('bond')->getClientOriginalName() . time());
							$filename = $unique_name . "." . $request->file('bond')->getClientOriginalExtension();
							$request->file('bond')->move('travel-bonds', $filename);
						}
					}
				}
				else{
					Session::flash('error', 'Travelling bond is required.');
					return Redirect::back()->withInput();
				}
				if($filename != ''){
					$travelOrder->bond = $filename;
				}
			}
		/*	$travelOrder->employee_id = $data['employee']->id;
			$travelOrder->save();
            DB::commit();

            Session::flash('success', 'Your travel order is saved');
            return redirect('travel-order/details'.'/'.$travelOrder->id);*/
		}
		else{
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$employee_id = Session::get('id');
		if(isset($employee_id)){
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$data['customView'] = 'custom-'.$data['employee']->custom_view.'.travel-order-details-top-buttons';

			$data['travelOrder'] = Travel_Order::find($id);
			if(count($data['travelOrder']) == 1){
				if($data['travelOrder']->employee->id == $data['employee']->id){
					$data['count'] = 1;
					return view('basic.travel-order-details', $data);
				}
				elseif($data['employee']->custom_view == 'accounts'){
					$data['count'] = 1;
					return view('basic.travel-order-details', $data);
				}
				else{
					foreach($data['voucher']->approvers as $approver){
						if($approver->id == $data['employee']->id){
							$data['count'] = 1;
							return view('basic.travel-order-details', $data);
						}
					}
					Session::flash('error', 'This travel order does not belongs to you.');
					return Redirect::back();
				}
			}
			else{
				Session::flash('error', 'Target does not exist');
				return Redirect::back();
			}
		}
		else{
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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

	public function sendApproval($id){
		$employee_id = Session::get('id');
		if(isset($employee_id)){
			$data['voucher'] = Travel_Order::find($id);
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$data['title'] = 'Travel Order';
			$data['forwardingUrl'] = 'travel-order';
			$voucherController =  new VoucherController();
			if($data['voucher']->travel_order_type == 'Foreign'){
				$data['approvers'] = $voucherController->twoLevelApproval($data['voucher'], $data['voucher']->employee->level);
			}
			elseif($data['voucher']->travel_order_type == 'Local'){
				$data['approvers'] = $voucherController->getApprovers($data['voucher'], $data['voucher']->employee->level+1);
			}
			else{
				Session::flash('error', 'No rule has been designed for the category of your travel order. Please contact system administrator');
				return Redirect::back();
			}
			$data['customLayout'] = 'custom-'.$data['employee']->custom_view.'.layout.ers-layout';
			return view('basic.first-approval', $data);
		}
		else{
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function firstApproval(Request $request, $id){
		$employee_id = Session::get('id');
		if(isset($employee_id)){
			$approval = Travel_Order_Employee::where('travel_order_id', '=', $id)->where('employee_id', '=', $request->input('approver'))->get();
			if(count($approval) == 0){
				DB::beginTransaction();
				$travelOrderApproval = new Travel_Order_Employee();
				$travelOrderApproval->travel_order_id = $id;
				$travelOrderApproval->employee_id = $request->input('approver');
				$travelOrderApproval->save();

				$travelOrder = Travel_Order::find($id);
				$travelOrder->status = 'Submitted';
				$travelOrder->save();

				DB::commit();

				$data['client'] = Employee::find($request->input('approver'));
				$data['employee'] = Employee::find($employee_id);
				$data['travelOrder'] = $travelOrder;

				/*if(isset($data['client']->email)){
					if(isset($data['employee']->email)){
						$mail = new MailController();
						$mail->send($data, $data, 'voucher-submitted', 'Voucher Submitted by '.$data['employee']->employee_name);
					}
					else{
						$mail = new MailController();
						$mail->send($data, null, 'voucher-submitted', 'Voucher Submitted by '.$data['employee']->employee_name);
					}

				}*/

				Session::flash('success', 'Travel order sent for approval successfully');
				return redirect('travel-order/details'.'/'.$id);
			}
			else{
				Session::flash('error', 'Already sent for approval');
				return redirect('travel-order/details'.'/'.$id);
			}
		}
		else{
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function approveTravelOrder($id){
		$employee_id = Session::get('id');
		if(isset($employee_id)){
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$travelOrder = Travel_Order::find($id);
			$data['employee'] = Employee::find($employee_id);
			$data['travelOrder'] = $travelOrder;
			$data['forwardingUrl'] = 'travel-order';

			$data['approval'] = Travel_Order_Employee::where('employee_id', '=', $data['employee']->id)->where('travel_order_id', '=', $travelOrder->id)->first();

			$maximumLevel = Approval_Level::max('level');

			DB::beginTransaction();
			if($travelOrder->travel_order_type == 'Foreign'){
				if($maximumLevel-$data['employee']->level == 0){
					$data['approval']->approved = 1;
					$data['approval']->save();

					$data['travelOrder']->status = 'Approved';
					$data['travelOrder']->save();

					$data['client'] = Employee::find($data['travelOrder']->employee->id);

					/*$mail = new MailController();
					$mail->send($data,$data,'voucher-approved','Voucher Approved');*/
				}
				elseif($maximumLevel-$data['employee']->level == 1){
					if($data['approval']->employee_id == $travelOrder->employee_id){
						$data['approvers'] = Employee::where('level', '=', $maximumLevel)->get();
						return view('basic.second-approval', $data);
					}
					else{
						$data['approval']->approved = 1;
						$data['approval']->save();
						$data['travelOrder']->status = 'Approved';
						$data['travelOrder']->save();

						$data['client'] = Employee::find($data['travelOrder']->employee->id);

						/*$mail = new MailController();
						$mail->send($data,$data,'voucher-approved','Voucher Approved');*/
					}
				}
				else{
					$department = Department::where('cost_center', '=', $travelOrder->charge_to_cost_center)->get();
					$businessUnit = Business_Unit::find($department[0]->businessUnit->id);

					$data['approvers'] = $businessUnit->levelApprovers($maximumLevel-1)->get();

					$data['customView'] = 'custom-'.$data['employee']->custom_view.'.second-approval';
					$data['customLayout'] = 'custom-'.$data['employee']->custom_view.'.layout.ers-layout';

					return view('basic.second-approval', $data);
				}
			}
			else{
				$data['approval']->approved = 1;
				$data['approval']->save();

				$data['travelOrder']->status = 'Approved';
				$data['travelOrder']->save();

				$data['client'] = Employee::find($data['travelOrder']->employee->id);

				/*$mail = new MailController();
				$mail->send($data,$data,'voucher-approved','Voucher Approved');*/

			}
			DB::commit();
			Session::flash('success', 'Voucher approved successfully');
			return redirect('travel-orders/require/approval');
		}
		else{
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function secondTravelOrderApproval(Request $request, $id){
		$employee_id = Session::get('id');
		if(isset($employee_id)){
			DB::beginTransaction();
			$approval = Travel_Order_Employee::find($id);
			$approval->approved = 1;
			$approval->save();

			$travelOrderApproval = new Travel_Order_Employee();
			$travelOrderApproval->travel_order_id = $approval->travel_order_id;
			$travelOrderApproval->employee_id = $request->input('approver');
			$travelOrderApproval->save();

			$travelOrder = Travel_Order::find($approval->travel_order_id);

			$data['client'] = Employee::find($request->input('approver'));
			$data['employee'] = Employee::find($travelOrder->employee->id);

			$data['approvedBy'] = Employee::find($approval->employee_id);
			$data['travelOrder'] = $travelOrder;

			/*if(isset($data['client']->email)){
				$mail = new MailController();
				$mail->send($data, $data, 'voucher-foreign-approved', 'Voucher of '.$data['employee']->employee_name);
			}*/

			DB::commit();

			Session::flash('success', 'Travel Order sent for second approval successfuly.');
			return redirect('travel-orders/require/approval');
		}
		else{
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}
	public function approvedTravelOrders(){
		$employee_id = Session::get('id');
		if(isset($employee_id)){
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$data['customView'] = 'custom-'.$data['employee']->custom_view.'.approved-travel-orders';
			$data['customLayout'] = 'custom-'.$data['employee']->custom_view.'.layout.ers-layout';

			return view('basic.approved-travel-orders', $data);
		}
		else{
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function unApprovedTravelOrders(){
		$employee_id = Session::get('id');
		if(isset($employee_id)){
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$data['customView'] = 'custom-'.$data['employee']->custom_view.'.approved-travel-orders';
			$data['customLayout'] = 'custom-'.$data['employee']->custom_view.'.layout.ers-layout';

			return view('basic.unapproved-travel-orders', $data);
		}
		else{
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function rejectTravelOrder(Request $request, $id){
		$employee_id = Session::get('id');
		if(isset($employee_id)){
			$data['employee'] = Employee::find($employee_id);
			DB::beginTransaction();
			$approval = Travel_Order_Employee::where('travel_order_id', '=', $id)->where('employee_id', '=', $data['employee']->id)->first();
			if(isset($approval)){
				$travelOrder = Travel_Order::find($id);
				$travelOrder->status = 'Rejected';
				$travelOrder->save();

				$approval->approved = 0;
				$approval->comments = $request->input('rejection_comments');
				$approval->save();

				$data['rejectionComments'] = $request->input('rejection_comments');
				$data['client'] = Employee::find($travelOrder->employee->id);
				$data['travelOrder'] = $travelOrder;

				/*$mail = new MailController();
				$mail->send($data,$data,'voucher-rejected', 'Voucher #'.$voucher->id.' Rejected');*/
			}
			DB::commit();
			Session::flash('success', 'Voucher rejected successfuly');
			return redirect('dashboard');
		}
		else{
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}
}
