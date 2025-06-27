<?php

namespace App\Http\Controllers;

use App\Approval_Level;
use App\Business_Unit;
use App\Category;
use App\Company;
use App\Department;
use App\Employee;
use App\GL_Mapping;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RateController;

use App\Processed_Voucher;
use App\ProcessedTravelOrder;
use App\Vehicle;
use App\Voucher;
use App\Voucher_Employee;
use App\Voucher_Item;
use App\SAP_Sync;
use App\Entitlements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

use DateTime;

class VoucherController extends Controller
{

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$employee_id = Session::get('id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
			$data['customView'] = 'custom-' . $custom_view . '.vouchers';
			if ($data['path'] == 'vouchers') {
				$data['panelHeading'] = 'Claims';
				$data['forwardingURL'] = 'voucher';
				/*if($data['employee']->account_type == 0 || $data['employee']->account_type == 1){
																																																																				$data['vouchers'] = Voucher::whereNull('is_travel_order')->get();
																																																																			}
																																																																			else{*/
				$data['vouchers'] = $data['employee']->vouchers()->with('voucherStatus', 'employee')->get();
				//}
			} elseif ($data['path'] == 'travel-orders') {
				$data['panelHeading'] = 'Travel Orders';
				$data['forwardingURL'] = 'travel-order';
				/*if($data['employee']->account_type == 0 || $data['employee']->account_type == 1){
																																																																				$data['vouchers'] = Voucher::whereNotNull('is_travel_order')->get();
																																																																			}
																																																																			else{*/
				$data['vouchers'] = $data['employee']->travelOrders;
				//}
			}
			return view('basic.vouchers', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function approvedVouchers()
	{
		$employee_id = Session::get('id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
			$data['customView'] = 'custom-' . $custom_view . '.approved-vouchers';
			if ($data['path'] == 'vouchers/approved') {
				$data['panelHeading'] = 'Approved Claims';
				$data['forwardingURL'] = 'voucher';
				$data['vouchers'] = $data['employee']->approvedVouchers;
			} elseif ($data['path'] == 'travel-orders/approved') {
				$data['panelHeading'] = 'Approved Travel Orders';
				$data['forwardingURL'] = 'travel-order';
				$data['vouchers'] = $data['employee']->approvedTravelOrders;
			}
			return view('basic.approved-vouchers', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function unapprovedVouchers()
	{
		$employee_id = Session::get('id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
			$data['customView'] = 'custom-' . $custom_view . '.unapproved-vouchers';
			if ($data['path'] == 'vouchers/unapproved') {
				$data['panelHeading'] = 'Unapproved Claims';
				$data['forwardingURL'] = 'voucher';
				$data['vouchers'] = $data['employee']->unapprovedVouchers;
			} elseif ($data['path'] == 'travel-orders/unapproved') {
				$data['panelHeading'] = 'Unapproved Travel Orders';
				$data['forwardingURL'] = 'travel-order';
				$data['vouchers'] = $data['employee']->unapprovedTravelOrders;
			}
			return view('basic.unapproved-vouchers', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($companyKey = null)
	{
		$employee_id = Session::get('id');
		$employee_number = Session::get('employee_number');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$data['employee_sf_data'] = SAP_Sync::where('employee_number', $employee_number)->first();

			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
			$data['customView'] = 'custom-' . $custom_view . '.add-voucher';

			$data['departments'] = Department::whereHas('businessUnit.company', function ($query) use ($data) {
				$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
			})->get();

			if ($data['path'] == 'voucher/add' || $data['path'] == 'voucher/add/company/{id}') {
				$data['panelHeading'] = 'New Claim';
				$data['storingURL'] = 'voucher/add';
				$data['type'] = 'voucher';

				if ($data['employee']->department->businessUnit->company->id == "45") {
					if ($data['employee']->grade_id >= "339" && $data['employee']->grade_id <= "343") {
						$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
							$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
						})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('voucher_suitable', '=', 1)->get();
					} else {
						$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
							$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
						})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('voucher_suitable', '=', 1)->where('category_name', '<>', 'Fuel - Mileage (out of city)')->where('category_name', '<>', 'Fuel - Mileage (within city)')->where('category_name', '<>', 'Fuel - Receipts')->get();
					}
				} else {
					$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
						$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
					})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('voucher_suitable', '=', 1)->get();
					if ($data['employee']->department->BusinessUnit->company->id == 1000 || $data['employee']->department->BusinessUnit->company->id == 1100 || $data['employee']->department->BusinessUnit->company->id == 1500)
					    $data['cost_centers'] = DB::table('cost_center_approvers')->where('company_id', $data['employee']->department->BusinessUnit->company->id)->get();

				}
				$data['vouchers'] = $data['employee']->unapprovedVouchers;
			} else if ($data['path'] == 'medical-claim/add') {
				$data['panelHeading'] = 'New Medical Claim';
				$data['storingURL'] = 'voucher/add';
				$data['type'] = 'voucher';

				if ($data['employee']->department->businessUnit->company->id == "45") {
					if ($data['employee']->grade_id >= "339" && $data['employee']->grade_id <= "343") {
						$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
							$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
						})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('voucher_suitable', '=', 1)->get();
					} else {
						$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
							$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
						})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('voucher_suitable', '=', 1)->where('category_name', '<>', 'Fuel - Mileage (out of city)')->where('category_name', '<>', 'Fuel - Mileage (within city)')->where('category_name', '<>', 'Fuel - Receipts')->get();
					}
				} else {
					$data['categories'] = Category::with('company')
						->whereHas('company', function ($query) use ($data) {
							$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
						})
						->where('category_name', 'LIKE', 'Medical%')
						->where('enabled', '=', 1)
						->where('voucher_suitable', '=', 1)
						->orderBy('category_name', 'ASC')
						->get();
				}
				$data['vouchers'] = $data['employee']->unapprovedVouchers;
				$data['entitlements'] = DB::table('entitlements')
					->where('entitlement_type', 'Medical')
					->where('employee_number', $data['employee']->employee_number)
					->selectRaw('SUM(consumed_limit) as consumed_limit, SUM(total_limit) as total_limit')
					->first();
				// $data['entitlements'] = [
				//                 'total_limit' => $data['entitlements']->sum('total_limit'),
				//                 'consumed_limit' => $data['entitlements']->sum('consumed_Limit')
				//             ];
				return view('basic.add-voucher-medical', $data);
			} else if ($data['path'] == 'travel-claim/add') {
				$data['panelHeading'] = 'New Travel Claim';
				$data['storingURL'] = 'voucher/add';
				$data['type'] = 'voucher';

				if ($data['employee']->department->businessUnit->company->id == "45") {
					if ($data['employee']->grade_id >= "339" && $data['employee']->grade_id <= "343") {
						$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
							$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
						})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('voucher_suitable', '=', 1)->get();
					} else {
						$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
							$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
						})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('voucher_suitable', '=', 1)->where('category_name', '<>', 'Fuel - Mileage (out of city)')->where('category_name', '<>', 'Fuel - Mileage (within city)')->where('category_name', '<>', 'Fuel - Receipts')->get();
					}
				} else {
					$data['categories'] = Category::with('company')
						->whereHas('company', function ($query) use ($data) {
							$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
						})
						->where('category_name', 'LIKE', 'Medical%')
						->where('enabled', '=', 1)
						->where('voucher_suitable', '=', 1)
						->orderBy('category_name', 'ASC')
						->get();
					if ($data['employee']->department->BusinessUnit->company->id == 1000 || $data['employee']->department->BusinessUnit->company->id == 1100 || $data['employee']->department->BusinessUnit->company->id == 1500)
					    $data['cost_centers'] = DB::table('cost_center_approvers')->where('company_id', $data['employee']->department->BusinessUnit->company->id)->get();
				}
				$data['vouchers'] = $data['employee']->unapprovedVouchers;
				$data['entitlements'] = DB::table('entitlements')->where('entitlement_type', 'Medical')->where('employee_number', $data['employee']->employee_number)->first();
				$data["approvedUnusedTravelOrders"] = $data["employee"]->approvedUnusedTravelOrders;
				return view('basic.add-travel-claim', $data);
			} else if ($data['path'] == 'fuel-claim/add') {
				$data['panelHeading'] = 'New Fuel Reimbursement Claim';
				$data['storingURL'] = 'voucher/add';
				$data['type'] = 'voucher';

				if ($data['employee']->department->businessUnit->company->id == "45") {
					if ($data['employee']->grade_id >= "339" && $data['employee']->grade_id <= "343") {
						$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
							$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
						})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('voucher_suitable', '=', 1)->get();
					} else {
						$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
							$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
						})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('voucher_suitable', '=', 1)->where('category_name', '<>', 'Fuel - Mileage (out of city)')->where('category_name', '<>', 'Fuel - Mileage (within city)')->where('category_name', '<>', 'Fuel - Receipts')->get();
					}
				} else {
					$data['categories'] = Category::with('company')
						->whereHas('company', function ($query) use ($data) {
							$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
						})
						->where('category_name', 'LIKE', 'Fuel%')
						->where('enabled', '=', 1)
						->where('voucher_suitable', '=', 1)
						->orderBy('category_name', 'ASC')
						->get();
					if ($data['employee']->department->BusinessUnit->company->id == 1000 || $data['employee']->department->BusinessUnit->company->id == 1100 || $data['employee']->department->BusinessUnit->company->id == 1500)
					    $data['cost_centers'] = DB::table('cost_center_approvers')->where('company_id', $data['employee']->department->BusinessUnit->company->id)->get();	
				}
				$data['vouchers'] = $data['employee']->unapprovedVouchers;
				$data['entitlements'] = DB::table('entitlements')->where('entitlement_type', 'Fuel')->where('employee_number', $data['employee']->employee_number)->first();
				$data['vehicle'] = DB::table('vehicles')->where('employee_id', $data['employee']->id)->first();
				return view('basic.add-voucher-fuel', $data);
			} else if ($data['path'] == 'fuel-mileage/add') {
				$data['panelHeading'] = 'New Fuel Reimbursement Claim';
				$data['storingURL'] = 'voucher/add';
				$data['type'] = 'voucher';

				if ($data['employee']->department->businessUnit->company->id == "45") {
					if ($data['employee']->grade_id >= "339" && $data['employee']->grade_id <= "343") {
						// dd("Here");
						$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
							$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
						})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('voucher_suitable', '=', 1)->get();
					} else {
						$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
							$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
						})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('voucher_suitable', '=', 1)->where('category_name', '<>', 'Fuel - Mileage (out of city)')->where('category_name', '<>', 'Fuel - Mileage (within city)')->where('category_name', '<>', 'Fuel - Receipts')->get();
					}
				} else {
					$data['categories'] = Category::with('company')
						->whereHas('company', function ($query) use ($data) {
							$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
						})
						->where('category_name', 'LIKE', 'Fuel%')
						->where('enabled', '=', 1)
						->where('voucher_suitable', '=', 1)
						->orderBy('category_name', 'ASC')
						->get();
					if ($data['employee']->department->BusinessUnit->company->id == 1000 || $data['employee']->department->BusinessUnit->company->id == 1100 || $data['employee']->department->BusinessUnit->company->id == 1500)
					    $data['cost_centers'] = DB::table('cost_center_approvers')->where('company_id', $data['employee']->department->BusinessUnit->company->id)->get();	
				}
				$data['vouchers'] = $data['employee']->unapprovedVouchers;
				$data['entitlements'] = DB::table('entitlements')->where('entitlement_type', 'Fuel')->where('employee_number', $data['employee']->employee_number)->first();
				$data['vehicle'] = DB::table('vehicles')->where('employee_id', $data['employee']->id)->first();
				return view('basic.add-voucher-fuel-mileage', $data);
			} elseif ($data['path'] == 'travel-order/add' || $data['path'] == 'voucher/add/company/{id}') {
				$data['panelHeading'] = 'New Travel Order';
				$data['storingURL'] = 'travel-order/add';
				$data['type'] = 'travel-order';
				$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
					$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
				})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('travel_order_suitable', '=', 1)->get();
				$data['vouchers'] = $data['employee']->unapprovedVouchers;
				if ($data['employee']->department->BusinessUnit->company->id == 1000 || $data['employee']->department->BusinessUnit->company->id == 1100 || $data['employee']->department->BusinessUnit->company->id == 1500)
					    $data['cost_centers'] = DB::table('cost_center_approvers')->where('company_id', $data['employee']->department->BusinessUnit->company->id)->get();
				return view('basic.add-travel-order', $data);
			}

			// 			dd($data['employee_sf_data']->line_manager);
			// dd($data['categories']);
			return view('basic.add-voucher', $data);
		} else {
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
// 		dd($request->input());
		// 		dump($request->file());
		$data['path'] = Route::getFacadeRoot()->current()->uri();
		$data['employee'] = Employee::find(Session::get('id'));
		$data['employee_sync'] = SAP_Sync::where('employee_number', $data['employee']->employee_number)->first();
		$itemCount = $request->input('itemCount');
		$voucher_id = $request->input('voucher_id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		DB::beginTransaction();

		$voucher = new Voucher();
		$voucher->submission_date = date('Y-m-d');
		$voucher->status = 'Draft';
		$voucher->employee_id = $data['employee']->id;
		$department = Department::find($request->input('cost_center'));
// 		dump($request->input('cost_center'));
// 		dump((!empty($request->input('cost_center')) && $request->input('cost_center') != '0'));
		$charge_to_cost_center = (!empty($request->input('charged_cost_center')) && $request->input('charged_cost_center') != '0') ? $request->input('charged_cost_center') : $data['employee_sync']->cost_center;
// 		dd($charge_to_cost_center);
		$voucher->charge_to_cost_center = $charge_to_cost_center;
		if ($charge_to_cost_center) {
			// $voucher->department_id = $department->id;
			if ($data['path'] == 'travel-order/add') {
				$voucher->is_travel_order = 1;
			}
			if (isset($voucher_id)) {
				$voucher->parent_id = $voucher_id;
				$voucher->save();

				$voucherParent = Voucher::find($voucher_id);
				$voucherParent->child_id = $voucher->id;
				$voucherParent->save();
			} else {
				// dump($voucher);
				$voucher->save();
			}
			for ($i = 1; $i <= $itemCount; $i++) {
				// dump($i);
				$category = Category::find($request->input('category_' . $i));
				if (isset($category->category_name)) {
					// dump($category->category_name);
					// dump($request->input('amount_'.$i));
					// dump("here");
					// dump($voucher);
					// dump($category);
					// dump($voucher->is_travel_order);
					// dump($request->input('amount_'.$i));
					// dd($category->travel_order_suitable);
					if ($request->input('amount_' . $i) > 0 || ($voucher->is_travel_order == 1 && $category->travel_order_suitable == 1) || $request->input('amount_' . $i) == null) {
						// dump("AMOUNT FOUND!");
						if (isset($category->duplication_check)) {
							if (isset($voucher_id)) {
								if ($voucherParent->status == 'Rejected' && (!isset($voucherParent->child_id) || !isset($voucherParent->parent_id))) {
									goto normalProcess;
								} else {
									goto checkDuplicate;
								}
							}
							checkDuplicate:
							$items = Voucher_Item::join('vouchers', 'vouchers.id', '=', 'voucher_items.voucher_id')
								->where('category_id', '=', $category->id)
								->where('date_from', '=', date('Y-m-d', strtotime($request->input('date_' . $i))) . ' 00:00:00')
								->where('receipt_date', '=', date('Y-m-d', strtotime($request->input('date_' . $i))) . ' 00:00:00')
								->where('amount', '=', $request->input('amount_' . $i))
								->where('vouchers.employee_id', '=', $data['employee']->id)
								->where(function ($query) use ($request, $i) {
									$mealDates = $request->input('meal_dates_' . $i);
									if (isset($mealDates)) {
										$query->where('description', '=', $request->input('description_' . $i));
									}
								})
								->get();

							$vehicle = $request->input('vehicle_number_' . $i);
							if (isset($vehicle)) {
								$vehicleDetails = Vehicle::where('vehicle_number', '=', $vehicle)->where('employee_id', '=', $data['employee']->id)->first();
								if (!isset($vehicleDetails)) {
									Session::flash('error', 'Vehicle number does not exist in system and thus excluded.');
									goto exclude;
								}
							}
							if (count($items) == 0) {
								// dump("Here");
								normalProcess:

								$voucherItem = new Voucher_Item();
								$dateRange = $request->input('date_range_' . $i);
								// dump($dateRange);
								$time = $request->input('time_' . $i);
								if (isset($dateRange)) {
									$dateRange = preg_split('/\s-\s/', $dateRange);

									$dateFrom = trim($dateRange[0]);
									$dateTo = trim($dateRange[1]);

									$voucherItem->date_from = date('Y-m-d H:i:s', strtotime($dateFrom));
									$voucherItem->date_to = date('Y-m-d H:i:s', strtotime($dateTo));

									// dump($dateRange);
									// dd($voucherItem->date_from, $voucherItem->date_to);
								} else {
									if (isset($time)) {
										$voucherItem->date_from = $request->input('date_' . $i) . ' ' . $request->input('time_' . $i);
									} else {
										$voucherItem->date_from = $request->input('date_' . $i);
									}
								}
								// dump("VOUCHER ITEM 1");
								// dump($voucherItem);

								if ($category->category_name == 'Medical - OPD') {
									$date = $request->input('date_' . $i);
									$currentDate = (new DateTime())->format('Y-m-d');
									if ($date > $currentDate) {
										Session::flash('error', 'Can not post medical claim for a future date');
										return Redirect::back();
									}
								}
								if ($category->category_name == 'Foreign - DA') {
									// dump($category->category_name);
									// $currency = $request->input('currency_'.$i);
									$currency = 'USD';
									// dump($voucherItem->date_from);
									// dump($voucherItem->date_to);

									$eligibleDADays = $this->calculateDADays($voucherItem->date_from, $voucherItem->date_to);
									$rateController = new RateController();
									$rate = $rateController->getCategoryRateDA($category->category_name, date('Y-m-d', strtotime($dateFrom)));
									$personal_days = $request->input('personal_days_' . $i);
									$accomodation = $request->input('accomodation_food_' . $i);

									$eligibleDADays = $eligibleDADays - $personal_days;

									if ($accomodation == "Own Accomodation")
										$eligibleDADays = $eligibleDADays;
									else if ($accomodation == "Accomodation by Host")
										$eligibleDADays = $eligibleDADays / 2;
									else if ($accomodation == "Both Accomodation & Food by Host")
										$eligibleDADays = $eligibleDADays / 3;


									if ($personal_days > $eligibleDADays) {
										Session::flash('error', 'Personal Days can not be more then the eligible DA');
									}
								// 	dump($eligibleDADays * $rate[0]->amount);
								// 	dump($currency);
									if (isset($currency)) {
										if ($currency == 'PKR') {
											$voucherItem->required_cur = 'PKR';
											goto convertUSDtoPKR;
										} elseif ($currency == 'USD') {
											// $voucherItem->forex_amount = $request->input('amount_'.$i);
											$voucherItem->forex_amount = $eligibleDADays * $rate[0]->amount;
											$voucherItem->cur = 'USD';
											$voucherItem->required_cur = 'USD';
										}
									} else {
										// dump("Inside else");
										convertUSDtoPKR:
										$travel_order = Voucher_Item::where('voucher_id', $request->input('travel_order_' . $i))
											->get();
										$conversion_rate = $travel_order[0]->amount / $travel_order[0]->forex_amount;

										$voucherItem->forex_amount = $request->input('amount_' . $i);
										$voucherItem->amount = $request->input('amount_' . $i) * $conversion_rate;
										$voucherItem->cur = 'USD';
									}
								} else if ($category->category_name == 'Foreign - Expenses') {

									$option = $request->input('exc_' . $i);
									if ($option == 1) {
										$conversion = $request->input('manual_exchange_rate_' . $i);
										$voucherItem->evidence = 1;
									} else if ($option == 0) {

										$url = 'http://data.fixer.io/api/' . date('Y-m-d', strtotime($voucherItem->date_from)) . '?access_key=466ea18406c2ed1bbefbd39b783dd005&symbols=' . $request->input('cur_' . $i) . ',PKR';
										$getForexRates = curl_init($url);
										curl_setopt($getForexRates, CURLOPT_RETURNTRANSFER, true);
										$result = json_decode(curl_exec($getForexRates));
										curl_close($getForexRates);
										if ($request->input('cur_' . $i) == 'USD') {
											$conversion = $result->rates->PKR / $result->rates->USD;
										} elseif ($request->input('cur_' . $i) == 'EUR') {
											dd($result);
											$conversion = $result->rates->PKR / $result->rates->EUR;
										} elseif ($request->input('cur_' . $i) == 'GBP') {
											$conversion = $result->rates->PKR / $result->rates->GBP;
										} elseif ($request->input('cur_' . $i) == 'CAD') {
											$conversion = $result->rates->PKR / $result->rates->CAD;
										} elseif ($request->input('cur_' . $i) == 'ZAR') {
											$conversion = $result->rates->PKR / $result->rates->ZAR;
										} elseif ($request->input('cur_' . $i) == 'AED') {
											$conversion = $result->rates->PKR / $result->rates->AED;
										} elseif ($request->input('cur_' . $i) == 'SAR') {
											$conversion = $result->rates->PKR / $result->rates->SAR;
										} elseif ($request->input('cur_' . $i) == 'CHF') {
											$conversion = $result->rates->PKR / $result->rates->CHF;
										} elseif ($request->input('cur_' . $i) == 'TRY') {
											$conversion = $result->rates->PKR / $result->rates->TRY;
										} elseif ($request->input('cur_' . $i) == 'CNY') {
											$conversion = $result->rates->PKR / $result->rates->CNY;
										} elseif ($request->input('cur_' . $i) == 'SEK') {
											$conversion = $result->rates->PKR / $result->rates->SEK;
										} elseif ($request->input('cur_' . $i) == 'KRW') {
											$conversion = $result->rates->PKR / $result->rates->KRW;
										} elseif ($request->input('cur_' . $i) == 'SGD') {
											$conversion = $result->rates->PKR / $result->rates->SGD;
										} elseif ($request->input('cur_' . $i) == 'UZS') {
											$conversion = $result->rates->PKR / $result->rates->UZS;
										} elseif ($request->input('cur_' . $i) == 'KZT') {
											$conversion = $result->rates->PKR / $result->rates->KZT;
										} elseif ($request->input('cur_' . $i) == 'KES') {
											$conversion = $result->rates->PKR / $result->rates->KES;
										} elseif ($request->input('cur_' . $i) == 'THB') {
											$conversion = $result->rates->PKR / $result->rates->THB;
										}
										$voucherItem->evidence = 0;
									}
									$voucherItem->forex_amount = $request->input('forex_amount_' . $i);
									$voucherItem->amount = $request->input('amount_' . $i) * $conversion;
									$voucherItem->cur = $request->input('cur_' . $i);
								} else if ($category->category_name == 'Mobile - Handset') {
									$fromDate = $request->input('date_' . $i);
									$last_availed = DB::table('vouchers')
                                        ->join('voucher_items', 'vouchers.id', '=', 'voucher_items.voucher_id')
                                        ->join('categories', 'voucher_items.category_id', '=', 'categories.id')
                                        ->where('categories.category_name', 'Mobile - Handset')
                                        ->where('vouchers.employee_id', $data['employee']->id)
                                        ->orderBy('voucher_items.id', 'DESC')
                                        ->select('voucher_items.*')
                                        ->first();
                                    
                                    // dd($last_availed);
								// 	$last_availed = DB::table('mobile_allowence')
								// 		->where('employee_number', $data['employee']->employee_number)
								// 		->orderBy('date', 'desc')
								// 		->first();

									$currentDate = new DateTime();
									if ($last_availed && isset($last_availed->date_from)) {
										$lastAvailedDate = new DateTime($last_availed->date_from);
								// 		dump($lastAvailedDate);
										$difference = $currentDate->diff($lastAvailedDate);
                                        // dd($difference);
										if ($difference->y < 3) {
											Session::flash('error', 'You have already consumed ' . $category->category_name . ' within the 3-year gap');
											return Redirect::back();
										} else {
											$rateController = new RateController();
											$mobile_entitlement = $rateController->getCategoryRateDA($category->category_name, date('Y-m-d', strtotime($fromDate)));

											if (isset($mobile_entitlement)) {
												// $voucherItem->amount = $mobile_entitlement[0]->amount;
												if($data['employee_sync']->company == "1400")
											        $voucherItem->amount = ($mobile_entitlement[0]->amount > $request->input('amount_' . $i)) ? $request->input('amount_' . $i) : $mobile_entitlement[0]->amount;
											    else
											        $voucherItem->amount = $mobile_entitlement[0]->amount;
											} else {
												Session::flash('error', 'You are not entitled to claim ' . $category->category_name);
												return Redirect::back();
											}
										}
									} else {
										$rateController = new RateController();
										$mobile_entitlement = $rateController->getCategoryRateDA($category->category_name, date('Y-m-d', strtotime($fromDate)));

										if (isset($mobile_entitlement)) {
								// 			$voucherItem->amount = $mobile_entitlement[0]->amount;
								            if($data['employee_sync']->company == "1400")
											    $voucherItem->amount = ($mobile_entitlement[0]->amount > $request->input('amount_' . $i)) ? $request->input('amount_' . $i) : $mobile_entitlement[0]->amount;
											else
											    $voucherItem->amount = $mobile_entitlement[0]->amount;
										} else {
											Session::flash('error', 'You are not entitled to claim ' . $category->category_name);
											return Redirect::back();
										}
									}
									// dump($fromDate);

									// 	dd($mobile_entitlment[0]->amount);
								} else if ($category->category_name == 'Fuel - Mileage (within city)' || $category->category_name == 'Fuel - Mileage (out of city)' || $category->category_name == 'Fuel - Mileage') {
									$fromDate = $request->input('date_' . $i);
									$mode_of_travel = $request->input('mode_of_travel_' . $i);
									$kms = $request->input('kms_' . $i);
									$from = $request->input('from_' . $i);
									$to = $request->input('to_' . $i);
									// dump($fromDate);
									// dump($mode_of_travel);
									// dump($kms);
									$rateController = new RateController();
									$mileage_total = $rateController->getCategoryRateDA($mode_of_travel, date('Y-m-d', strtotime($fromDate)));
									if (isset($mileage_total)) {
										// dump($mileage_total);
										// $voucherItem->description = "Travel from " . $from. "to " . $to. ". Total Distance travelled was " . $kms. "Kms";
										// dd($voucherItem->description);
										$voucherItem->amount = $kms * $mileage_total[0]->amount;
									}

									// dump($mileage_total);
									else {
										Session::flash('error', 'You are not entitled to claim' . $category->category_name . '');
									}
									// dd($voucherItem->amount);
								} else if ($category->category_name == 'Local - DA' || ($category->category_name == 'Local - DA (via Air)' && $data['path'] != 'travel-order/add')) {
									$eligibleDADays = $this->calculateDADays($voucherItem->date_from, $voucherItem->date_to);
									// 	dump($eligibleDADays);
									// 	dump($category->category_name);
									// 	dump(date('Y-m-d', strtotime($dateFrom)));
									$rateController = new RateController();
									$rate = $rateController->getCategoryRateDA($category->category_name, date('Y-m-d', strtotime($dateFrom)));
									// 	dump($rate);
									$voucherItem->amount = $eligibleDADays * $rate[0]->amount;
								} else {
									$voucherItem->amount = $request->input('amount_' . $i);
									$voucherItem->cur = $request->input('currency');
								}
								// dump("VOUCHER ITEM 2");
								// dump($voucherItem);
								$travelOrder = $request->input('travel_order_' . $i);
								if (isset($travelOrder)) {
									$order = Voucher::find($travelOrder);
									if (isset($order) && $order->status == 'Posted') {
										$order->status = 'Adjusted';
										$order->save();
										// dump($order->totalAmount($order->id));
										// dump($voucherItem->amount);
										$voucherItem->travel_order_id = $travelOrder;
										$voucherItem->advance_amount = $order->totalAmount($order->id);
								// 		dump($voucherItem->amount);
								// 		dd($order->totalAmount($order->id));
										$voucherItem->amount = isset($voucherItem->amount) ? $voucherItem->amount - $order->totalAmount($order->id) : 0;
										// 		$voucherItem->amount = 0;
									}
								}
								$voucherItem->travel_mode = $request->input('mode_of_travel_' . $i);
								$voucherItem->receipt_number = $request->input('recipt_number_' . $i);
								$voucherItem->country = $request->input('country_' . $i);
								$voucherItem->city = $request->input('city_' . $i);
								$voucherItem->receipt_date = $request->input('date_' . $i);
								$voucherItem->receipt_copy = $request->input('receipt_provided_' . $i);
								$voucherItem->amount_paid = $request->input('amount_paid_' . $i);
								$voucherItem->description = $request->input('description_' . $i);
								$voucherItem->month = $request->input('month_' . $i);
								$voucherItem->rate_per_litre = $request->input('rate_per_litre_' . $i);
								$ratePerLitre = (float) $request->input('rate_per_litre_' . $i);
								$amount = (float) $request->input('amount_' . $i);

								if ($ratePerLitre > 0 && $amount > 0) {
									$voucherItem->litres = $amount / $ratePerLitre;

									if ($request->input('claimType') == "Fuel") {
										if ($request->input('official_travel_' . $i) == "No") {
											$data['entitlements'] = DB::table('entitlements')->where('entitlement_type', 'Fuel')->where('employee_number', $data['employee']->employee_number)->first();
											$update_data = [];
											$update_data['consumed_limit'] = $data['entitlements']->consumed_limit + $voucherItem->litres;
											if ($update_data['consumed_limit'] > $data['entitlements']->total_limit) {
												Session::flash('error', 'You have already consumed your limit');
												return Redirect::back();
											}
											DB::table('entitlements')->where('id', $data['entitlements']->id)->update($update_data);
										}
									}
								}

								if ($request->input('claimType') == "Medical") {
									$claim_amount = $voucherItem->amount;
									if ($request->input('opd_claim_type_' . $i) == 'Dental' || $request->input('opd_claim_type_' . $i) == 'Optical') {
										$break_down = $request->input('opd_claim_type_' . $i);
									} else {
										$break_down = 'Others';

										if($request->input('opd_claim_type_' . $i) == "consultation"){
                                        	$claim_amount =$voucherItem->amount - (0.10 * $voucherItem->amount); 
                                        	$voucherItem->amount = $claim_amount;
                                        }
									}

									$data['entitlements'] = DB::table('entitlements')
										->where('entitlement_type', 'Medical')
										->where('break_down', $break_down)
										->where('employee_number', $data['employee']->employee_number)
										->orderBy('id', 'desc')
										->first();

									$update_data = [];
									$update_data['consumed_limit'] = $data['entitlements']->consumed_limit + $claim_amount;
									if ($update_data['consumed_limit'] > $data['entitlements']->total_limit) {
										if ($break_down == 'Others') {
											$data['entitlements'] = DB::table('entitlements')
												->where('entitlement_type', 'Medical')
												->where('break_down', 'Dental')
												->where('employee_number', $data['employee']->employee_number)
												->orderBy('id', 'desc')
												->first();

											$update_data['consumed_limit'] = $data['entitlements']->consumed_limit + $claim_amount;
											if ($update_data['consumed_limit'] > $data['entitlements']->total_limit) {
												$data['entitlements'] = DB::table('entitlements')
													->where('entitlement_type', 'Optical')
													->where('break_down', 'Dental')
													->where('employee_number', $data['employee']->employee_number)
													->orderBy('id', 'desc')
													->first();

												$update_data['consumed_limit'] = $data['entitlements']->consumed_limit + $claim_amount;
												if ($update_data['consumed_limit'] > $data['entitlements']->total_limit) {
													Session::flash('error', 'You have already consumed your limit');
													return Redirect::back();
												}
											} else {
												Session::flash('error', 'You have already consumed your limit');
												return Redirect::back();
											}
										} else {
											Session::flash('error', 'You have already consumed your limit');
											return Redirect::back();
										}
									}
									/*
																																																																																																																																																							   Not Updating here, will be updated after the approved amount is entered by the medical officer
																																																																																																																																																						   */
									// 	DB::table('entitlements')->where('id', $data['entitlements']->id)->update($update_data);

									$voucherItem->description = "OPD - Claim Type - " . $request->input('opd_claim_type_' . $i);
								}
								$voucherItem->medical_claim_type = $request->input('medical_claim_type_' . $i);
								$voucherItem->opd_claim_type = $request->input('opd_claim_type_' . $i);
								$voucherItem->rupees_in_words = $request->input('rupees_in_words_' . $i);
								$voucherItem->accomodation_food = $request->input('accomodation_food_' . $i);
								// $voucherItem->evidence = $request->input('evidence_'.$i);
								$voucherItem->purpose = $request->input('purpose_' . $i);
								$voucherItem->receipt = 'No Attachment';
								// $voucherItem->vehicle_number = $request->input('vehicle_no_'.$i);
								$voucherItem->vehicle_number = "ABC-123";
								$voucherItem->vehicle_registration_name = $request->input('vehicle_registration_name_' . $i);
								$voucherItem->official_travel = $request->input('official_travel_' . $i);
								$voucherItem->receipt_number = $request->input('receipt_number_' . $i);
								$voucherItem->kms = $request->input('kms_' . $i);
								$voucherItem->toll_tax = $request->input('toll_tax_' . $i);
								$voucherItem->remarks = $request->input('remarks_' . $i);
								$voucherItem->voucher_id = $voucher->id;
								$voucherItem->category_id = $request->input('category_' . $i);
								$voucherItem->from = $request->input('from_' . $i);
								$voucherItem->to = $request->input('to_' . $i);
								$voucherItem->personal_days = $request->input('personal_days_' . $i);
								$voucherItem->visa_required = $request->input('visa_required_' . $i);
								$voucherItem->airport_transaport = $request->input('airport_transport_' . $i);
								$voucherItem->hotel_required = $request->input('hotel_required_' . $i);
								$attachment = $request->file('attachment_' . $i);
								// dd($attachment);
								$extensions = array(
									'jpg',
									'png',
									'JPG',
									'PNG',
									'PDF',
									'pdf',
									'ZIP',
									'zip',
									'RAR',
									'rar',
									'docx',
									'DOCX',
									'PPTX',
									'pptx',
									'MP4',
									'mp4',
									'xls',
									'xlsx',
									'XLSX',
									'vcf',
									'jpeg',
									'JPEG'
								);

								global $filename;
								if ($attachment == NULL) {
									$filename = "No File";
								} else if ($attachment) {
									if ($attachment->getClientOriginalName()) {
										if (in_array($attachment->getClientOriginalExtension(), $extensions)) {
											$cleanedFilename = preg_replace('/[^A-Za-z0-9\-.]/', '', $attachment->getClientOriginalName());
											$filename = time() . $cleanedFilename;
											$attachment->move('claim-attachments', $filename);
										} else {
											Session::flash('error', 'File type is not supported!');
											return Redirect::back()
												->withInput();
										}
									}
								}

								$voucherItem->attachment = $filename;
								$voucherItem->receipt_copy = $filename == "No File" ? 0 : 1;

								$mealDates = $request->input('meal_dates_' . $i);
								if (isset($mealDates)) {
									$days = count(explode(',', $mealDates));
									$voucherItem->description = $request->input('description_' . $i) . ' - ' . $days . ' days';
									$rates = new RateController();
									$rate = $rates->getCategoryRate($request->input('description_' . $i), date('Y-m-d'));
									$rate = json_decode($rate)[0]->amount;
									$amount = $days * $rate;
									if ($amount != $voucherItem->amount) {
										$voucherItem->amount = $amount;
									}
									$voucherItem->meal_dates = $request->input('meal_dates_' . $i);
								}
								$chargeTo = $request->input('cost_center');
								if (isset($voucher->is_travel_order) && $voucher->is_travel_order == 1) {
									$glCode = GL_Mapping::where('category_id', '=', $request->input('category_' . $i))
										->where('classification', '=', 'TO')
										->get();
								} else {
									if ($chargeTo != '') {
										$department = Department::find($chargeTo);
										$glCode = GL_Mapping::where('category_id', '=', $request->input('category_' . $i))
											->where('classification', '=', $department->classification)
											->get();
									} else {
										$glCode = GL_Mapping::where('category_id', '=', $request->input('category_' . $i))
											->where('classification', '=', $data['employee']->department->classification)
											->get();
									}
								}
								if (count($glCode) > 0) {
									$voucherItem->gl_code = $glCode[0]->gl_account;
								}
								$voucherItem->save();
								exclude:
							} else {
								Session::flash('error', 'An expense Item of ' . $category->category_name . ' was a duplicate and thus not entered in the system.');
							}
						} else {
							goto normalProcess;
						}
					} else {
						Session::flash('error', 'An expense Item of ' . $category->category_name . ' was of PKR 0 and thus not saved');
					}
				}
			}
		} else {
			Session::flash('error', 'Wrong Cost Center Selected. Please try again.');
			return Redirect::back();
		}

		DB::commit();
		if ($data['path'] == 'travel-order/add') {
			Session::flash('success', 'Your travel order is saved');
			return redirect('travel-order/details' . '/' . $voucher->id);
		} elseif ($data['path'] == 'voucher/add') {
			Session::flash('success', 'Your claim is saved');
			return redirect('voucher/details' . '/' . $voucher->id);
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 */
	public function show($id)
	{
		$employee_id = Session::get('id');
		$employee_number = Session::get('employee_number');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$data['employee_sf_data'] = SAP_Sync::join('employees', 'sap_sync.employee_number', '=', 'employees.employee_number')
				->join('vouchers', 'employees.id', '=', 'vouchers.employee_id')
				->where('vouchers.id', $id)
				->select('sap_sync.*')
				->first();
			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
			$data['customView'] = 'custom-' . $custom_view . '.voucher-details-top-buttons';
			if ($data['path'] == 'voucher/details/{id}') {
				$data['forwardingURL'] = 'voucher';
				$data['type'] = 'voucher';
				$data['panelHeading'] = 'Claim';
				$data['layout'] = 'ers-layout';
			} elseif ($data['path'] == 'travel-order/details/{id}') {
				$data['forwardingURL'] = 'travel-order';
				$data['type'] = 'travel-order';
				$data['panelHeading'] = 'Travel Order';
				$data['layout'] = 'ers-layout';
			} elseif ($data['path'] == 'get/voucher/details') {
				$data['forwardingURL'] = 'voucher';
				$data['type'] = 'voucher';
				$data['panelHeading'] = 'Claim';
			}
			// return $data['path'];

			$allowed = [0, 1, 3];
			$data['voucher'] = Voucher::find($id);
			$data['company'] = $data['employee_sf_data']->employee->department->businessUnit->company;
			// dd($data);
			if (count($data['voucher']) == 1) {
				if ($data['voucher']->employee->id == $data['employee']->id) {
					$data['count'] = 1;
					return view('basic.voucher-details', $data);
				} elseif (in_array($data['employee']->account_type, $allowed)) {
					$data['count'] = 1;
					return view('basic.voucher-details', $data);
				} else {
					foreach ($data['voucher']->approvers as $approver) {
						if ($approver->id == $data['employee']->id) {
							$data['count'] = 1;
							return view('basic.voucher-details', $data);
						}
					}
					Session::flash('error', 'This voucher does not belongs to you.');
					return Redirect::back();
				}
			} else {
				Session::flash('error', 'Target does not exist');
				return Redirect::back();
			}
		} else {
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
		$employee_id = Session::get('id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$voucher = Voucher::find($id);
			if ($voucher->employee->id == $employee_id) {
				if (count($voucher->voucherStatus) > 0) {
					Session::flash('error', 'You cannot delete this voucher. It is in approval process.');
					return redirect('voucher/details' . '/' . $voucher->id);
				} else {
					//			 $voucherItem = DB::table('voucher_items')
					//              ->select('travel_order_id','id')
					//              ->where('voucher_id','=',$voucher->id)
					//              ->whereNotNull('travel_order_id')
					//              ->get();

					//             if(count($voucherItem)>0){
					//                 DB::table('vouchers')
					//                     ->where('id','=',$voucherItem[0]->travel_order_id)
					//                     ->update(['status' => 'Posted']);
					//               } 

					foreach ($voucher->voucherItems as $voucherItem) {
						if ($voucherItem->travel_order_id != NULL) {
							$freeTA = Voucher::find($voucherItem->travel_order_id);
							$freeTA->status = 'Posted';
							$freeTA->save();
						}
						$voucherItem->delete();
					}
					$voucher->delete();

					Session::flash('success', 'Claim Deleted Successfuly.');
					return redirect('dashboard');
				}
			} else {
				Session::flash('error', 'You do not have the access to delete the Voucher');
				return redirect('dashboard');
			}
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function formDisplay($category, $parent, $itemCount, $categoryID, Request $request)
	{
		$type = $request->input('type');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (view()->exists('category-forms.' . $type . '.' . $category)) {
			$data['categoryName'] = $category;
			$data['categoryID'] = $categoryID;
			$data['parent'] = $parent;
			$data['itemCount'] = $itemCount;
			$data['employee'] = Employee::find(Session::get('id'));
			$view = View::make('category-forms.' . $type . '.' . $category, $data)->render();
			$data['row'] = $view;
			return response(json_encode($data), 200);
		} else {
			return json_encode('Not Found', 404);
		}
	}

	public function addVoucherCategory(Request $request, $count)
	{
		$employee_id = Session::get('id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['count'] = $count;
			$data['employee'] = Employee::find($employee_id);
			$type = $request->input('type');
			$company = $request->input('company');
			if ($type == 'travel-order') {
				if ($company) {
					$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data, $company) {
						$query->where('companies.id', '=', $company);
					})->whereNotNull('travel_order_suitable')->orderby('category_name', 'ASC')->get();
				} else {
					$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
						$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
					})->whereNotNull('travel_order_suitable')->orderby('category_name', 'ASC')->get();
				}
			} else {
				if ($company) {
					$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data, $company) {
						$query->where('companies.id', '=', $company);
					})->whereNotNull('voucher_suitable')->orderby('category_name', 'ASC')->get();
				} else {
					$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
						$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
					})->whereNotNull('voucher_suitable')->orderby('category_name', 'ASC')->get();
				}
			}
			$view = View::make('basic.partials.add-category', $data)->render();
			$data['row'] = $view;
			return response(json_encode($data), 200);
		} else {
			return response(json_encode('Session Ended', 404));
		}
	}


	public function addVoucherItem($itemCount, $parent, $categoryID, Request $request)
	{
		$type = $request->input('type');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (view()->exists('basic.partials.' . $type . '.' . $parent)) {
			$data['itemCount'] = $itemCount;
			$data['categoryID'] = $categoryID;
			$data['employee'] = Employee::find(Session::get('id'));
			$view = View::make('basic.partials.' . $type . '.' . $parent, $data)->render();
			$data['row'] = $view;
			return response(json_encode($data), 200);
		} else {
			return response(json_encode('Not Found', 404));
		}
	}

	public function requireApproval()
	{
		$employee_id = Session::get('id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);

			return view('basic.vouchers-require-approval', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function approverApproved()
	{
		$employee_id = Session::get('id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			return view('basic.vouchers-approver-approved', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function approverDeclined()
	{
		$employee_id = Session::get('id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);

			return view('basic.vouchers-approver-declined', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function firstVoucherApprovalBUApproverWise(Request $request, $id)
	{
		$employee_id = Session::get('id');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			if ($data['path'] == 'voucher/first/approval/bu-wise/{id}') {
				$data['panelHeading'] = 'Claim';
				$data['forwardingURL'] = 'voucher';
			} elseif ($data['path'] == 'travel-order/first/approval/bu-wise/{id}') {
				$data['panelHeading'] = 'Travel Order';
				$data['forwardingURL'] = 'travel-order';
			}
			$approval = Voucher_Employee::where('voucher_id', '=', $id)->where('employee_id', '=', $request->input('approver'))->get();
			if (count($approval) == 0) {
				DB::beginTransaction();
				$voucherApproval = new Voucher_Employee();
				$voucherApproval->voucher_id = $id;
				$voucherApproval->employee_id = $request->input('approver');
				$voucherApproval->save();

				$voucher = Voucher::find($id);
				$voucher->status = 'Submitted';
				$voucher->save();

				DB::commit();

				$data['client'] = Employee::find($request->input('approver'));
				$data['employee'] = Employee::find($employee_id);
				$data['voucher'] = $voucher;

				if (isset($data['client']->email)) {
					if (isset($data['employee']->email)) {
						$mail = new MailController();
						$mail->send($data, $data, $data['forwardingURL'] . '-submitted', $data['panelHeading'] . ' Submitted by ' . $data['employee']->employee_name);
					} else {
						$mail = new MailController();
						$mail->send($data, null, $data['forwardingURL'] . '-submitted', $data['panelHeading'] . ' Submitted by ' . $data['employee']->employee_name);
					}
				}

				Session::flash('success', $data['panelHeading'] . ' sent for approval successfully');
				return redirect($data['forwardingURL'] . '/' . 'details' . '/' . $id);
			} else {
				Session::flash('error', 'Already sent for approval');
				return redirect($data['forwardingURL'] . '/' . 'details' . '/' . $id);
			}
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	private function getApprovalLevelForCategory($categoryName, $voucher)
	{
		$category = Category::where('category_name', $categoryName)->where('company_id', $voucher->employee->department->BusinessUnit->company->id)->first();
		// $category = Category::where('category_name', $categoryName)->where('company_id', 1700 )->first();
		return $category->approval_steps;
	}

	public function firstVoucherApproval(Request $request, $id)
	{
		$employee_id = Session::get('id');
		$employee_number = Session::get('employee_number');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');

		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			if ($data['path'] == 'voucher/first/approval/{id}') {
				$data['panelHeading'] = 'Claim';
				$data['forwardingURL'] = 'voucher';
			} elseif ($data['path'] == 'travel-order/first/approval/{id}') {
				$data['panelHeading'] = 'Travel Order';
				$data['forwardingURL'] = 'travel-order';
			}

			$voucher = Voucher::find($id);
			$voucher_item = DB::table("voucher_items")
				->where('voucher_id', $voucher->id)
				->first();
			$voucher_item->category = $voucher->categories($voucher->id)->first();
			$department = $voucher->department_id ? Department::find($voucher->department_id) : Department::find($voucher->employee->department_id);
			$businessUnit = $department->businessUnit;
			$voucher_employee = Employee::find($voucher->employee_id);

			if (in_array($voucher_employee->department->businessUnit->company->id, ['1000', '1100', '1500', '1200', '1300'])) {
				if (isset($employee_id)) {
					$data['voucher'] = Voucher::find($id);
					$data['voucher']->voucher_item = $voucher_item;
					// dd($data['voucher']);
					$data['path'] = Route::getFacadeRoot()->current()->uri();
					$data['employee'] = Employee::find($employee_id);
					if ($data['path'] == 'voucher/send/approval/{id}') {
						$data['forwardingURL'] = 'voucher';
						$data['panelHeading'] = 'Claim';
					} elseif ($data['path'] == 'travel-order/send/approval/{id}') {
						$data['forwardingURL'] = 'travel-order';
						$data['panelHeading'] = 'Travel Order';
					}
					$officiallyTravelled = 0;
					$alreadySubmitted = Voucher_Employee::where('voucher_id', '=', $id)->first();
					if (isset($alreadySubmitted)) {
						Session::flash('error', 'Submission has already been made for the selected voucher.');
						return Redirect::back();
					}
					$voucher_item_check = Voucher_Item::where('voucher_id', '=', $id)->first();
					// dump($id);
					// dd($voucher_item_check);
					if ($voucher_item_check == null) {
						Session::flash('error', 'No Expense is entered for the selected voucher.');
						return Redirect::back();
					} else {
						if (count($data['voucher']->categories($data['voucher']->id)) == 1 && $data['voucher']->categories($data['voucher']->id)[0]->category_name == 'Fuel - Receipts') {
							foreach ($data['voucher']->categories($data['voucher']->id) as $category) {
								foreach ($data['voucher']->categoryItems($data['voucher']->id, $category->id) as $voucherItem) {
									if ($voucherItem->official_travel == 'Yes') {
										$officiallyTravelled = 1;
									}
								}
							}
							if ($officiallyTravelled == 1) {
								goto processNormally;
							} else {
								$data['voucher']->status = 'Approved - Documents in transit';
								$voucherApproval = new Voucher_Employee();
								$voucherApproval->voucher_id = $id;
								$voucherApproval->employee_id = $data['employee']->id;
								$voucherApproval->approved = 1;
								$voucherApproval->save();
								$voucher = Voucher::find($id);
							    $voucher->status = 'Approved - Documents in transit';
								$voucher->save();

								$data['client'] = Employee::find($data['employee']->id);
                				$data['employee'] = Employee::find($employee_id);
                				$data['voucher'] = $voucher;
                
                				if (isset($data['client']->email)) {
                					if (isset($data['employee']->email)) {
                						$mail = new MailController();
                						$mail->send($data, $data, $data['forwardingURL'] . '-submitted', $data['panelHeading'] . ' Submitted by ' . $data['employee']->employee_name);
                					} else {
                						$mail = new MailController();
                						$mail->send($data, null, $data['forwardingURL'] . '-submitted', $data['panelHeading'] . ' Submitted by ' . $data['employee']->employee_name);
                					}
                				}
							
								Session::flash('success', 'Voucher has been approved. Please send the documents to accounts department');
								
								return redirect('voucher/details' . '/' . $data['voucher']->id);
							}
						} elseif (count($data['voucher']->categories($data['voucher']->id)) == 1 && $data['voucher']->categories($data['voucher']->id)[0]->category_name == 'Medicines - Mother' || $data['voucher']->categories($data['voucher']->id)[0]->category_name == 'Mobile - Handset') {
							$data['voucher']->status = 'Approved - Documents in transit';
							$voucherApproval = new Voucher_Employee();
							$voucherApproval->voucher_id = $id;
							$voucherApproval->employee_id = $data['employee']->id;
							$voucherApproval->approved = 1;
							$voucherApproval->save();
							$voucher = Voucher::find($id);
							$voucher->status = 'Approved - Documents in transit';
							
							$voucher->save();
							
                            $data['client'] = Employee::find($data['employee']->id);
            				$data['employee'] = Employee::find($employee_id);
            				$data['voucher'] = $voucher;
            
            				if (isset($data['client']->email)) {
            					if (isset($data['employee']->email)) {
            						$mail = new MailController();
            						$mail->send($data, $data, $data['forwardingURL'] . '-submitted', $data['panelHeading'] . ' Submitted by ' . $data['employee']->employee_name);
            					} else {
            						$mail = new MailController();
            						$mail->send($data, null, $data['forwardingURL'] . '-submitted', $data['panelHeading'] . ' Submitted by ' . $data['employee']->employee_name);
            					}
            				}
							
							Session::flash('success', 'Voucher has been approved. Please send the documents to accounts department');
							return redirect('voucher/details' . '/' . $data['voucher']->id);
						} elseif (count($data['voucher']->categories($data['voucher']->id)) == 1 && $data['voucher']->categories($data['voucher']->id)[0]->category_name == 'Motor Car Income Tax') {
						    $data['voucher']->status = 'Approved - Documents in transit';
							$voucherApproval = new Voucher_Employee();
							$voucherApproval->voucher_id = $id;
							$voucherApproval->employee_id = $data['employee']->id;
							$voucherApproval->approved = 1;
							$voucherApproval->save();
							$voucher = Voucher::find($id);
							$voucher->status = 'Approved - Documents in transit';
							$voucher->save();
                            
                            $data['client'] = Employee::find($data['employee']->id);
            				$data['employee'] = Employee::find($employee_id);
            				$data['voucher'] = $voucher;
            
            				if (isset($data['client']->email)) {
            					if (isset($data['employee']->email)) {
            						$mail = new MailController();
            						$mail->send($data, $data, $data['forwardingURL'] . '-submitted', $data['panelHeading'] . ' Submitted by ' . $data['employee']->employee_name);
            					} else {
            						$mail = new MailController();
            						$mail->send($data, null, $data['forwardingURL'] . '-submitted', $data['panelHeading'] . ' Submitted by ' . $data['employee']->employee_name);
            					}
            				}
							
							Session::flash('success', 'Voucher has been approved. Please send the documents to accounts department');
							return redirect('voucher/details' . '/' . $data['voucher']->id);
						} else {
							processNormally:
				// 			dd("Here1");
							if (count($data['voucher']->requiresSecondApproval()) > 0) {
								// dd("Here2");
								$data['approvers'] = $this->twoLevelApproval($data['voucher'], $data['voucher']->employee->level);
							} else {
								// dd("Here3");
								// 		$data['approvers'] = $this->getApprovers($data['voucher'], $data['voucher']->employee->level + 1);
								// 		$data['cost_center_approvers'] =  DB::table('cost_center_approvers')->where('cost_center', $data['voucher']->charge_to_cost_center)->first();
								// 	dump($data['approvers']);
								// 	dd($data['cost_center_approvers']);
								// dd($data['approvers']);
								$data['approvers'] = $this->getApprovers($data['voucher']);
							}
							
							$data['customLayout'] = 'custom-' . $data['employee']->custom_view . '.layout.ers-layout';
							return view('basic.first-approval', $data);
						}
					}
				} else {
					Session::flash('error', 'Your session has ended. Please login to continue.');
					return redirect('/');
				}
			}

			// Automatically approve if the claim is from the MD (IDs: 20, 50, 24) or check if the claim if of the BU Head
			$bu = DB::table('sap_sync')->where('employee_number', $employee_number)->first();
			$buHead = DB::table('bu_head')->where('bu_id', $bu->business_unit)->first();

			if (in_array($voucher->employee_id, [4092]) || $buHead->employee_number == $voucher->employee->employee_number) {
				$buHeadEmployee = Employee::where('employee_number', $buHead->employee_number)->first();

				$voucherApproval = new Voucher_Employee();
				$voucherApproval->voucher_id = $voucher->id;
				$voucherApproval->employee_id = $buHeadEmployee->id;
				$voucherApproval->approved = 1;
				$voucherApproval->save();

				$voucher = Voucher::find($id);
				$data['voucher'] = $voucher;

				$data['voucher']->status = 'Approved - Documents in transit';
				$data['voucher']->save();

				$mail = new MailController();
				$mail->send($data, $data, $data['forwardingURL'] . '-approved', $data['panelHeading'] . ' Approved');

				Session::flash('success', $data['panelHeading'] . ' sent for approval successfully');
				return redirect($data['forwardingURL'] . '/' . 'details' . '/' . $id);
			}
			$categories = $voucher->categories($voucher->id);

			$highestApprovalCategory = null;
			$maxApprovalLevel = 0;

			foreach ($categories as $category) {
				$categoryApprovalLevel = $this->getApprovalLevelForCategory($category->category_name, $voucher);
				if ($categoryApprovalLevel > $maxApprovalLevel) {
					$maxApprovalLevel = $categoryApprovalLevel;
					$highestApprovalCategory = $category->category_name;
				}
			}
			// dump($highestApprovalCategory);
			// dd($maxApprovalLevel);
			if ($category->category_name == "Medical - OPD") {
				$voucher = Voucher::find($id);
				$data['voucher'] = $voucher;

				DB::beginTransaction();
				$voucherApproval = new Voucher_Employee();
				$voucherApproval->voucher_id = $id;
				if ($voucher_employee->department->businessUnit->company->company_name == "Packages Limited" || $voucher_employee->department->businessUnit->company->company_name == "Packages Convertors Limited")
					$voucherApproval->employee_id = "4970";
				else if ($voucher_employee->department->businessUnit->company->company_name == "Tripack Films Limited")
					$voucherApproval->employee_id = "4971";
				$voucherApproval->save();

				$voucher->status = 'Submitted';
				$voucher->save();

				DB::commit();

				$data['client'] = Employee::find($voucherApproval->employee_id);
				$data['employee'] = Employee::find($employee_id);
				$data['voucher'] = $voucher;

				if (isset($data['client']->email)) {
					if (isset($data['employee']->email)) {
						$mail = new MailController();
						$mail->send($data, $data, $data['forwardingURL'] . '-submitted', $data['panelHeading'] . ' Submitted by ' . $data['employee']->employee_name);
					} else {
						$mail = new MailController();
						$mail->send($data, null, $data['forwardingURL'] . '-submitted', $data['panelHeading'] . ' Submitted by ' . $data['employee']->employee_name);
					}
				}

				Session::flash('success', $data['panelHeading'] . ' sent for approval successfully');
				return redirect($data['forwardingURL'] . '/' . 'details' . '/' . $id);
			}

			// Categories that follow the workflow: Employee >> Accounts >> Completed
			$categoriesWithDirectApproval = [
				// 'Fuel - Mileage (within city)',
				'Fuel - Receipts',
				// 'Fuel - Mileage (out of city)',
				// 'Local - Toll tax / Parking charges / E-Tag (out of city)',
				// 'Medicines - Mother'
			];

			// if (in_array($highestApprovalCategory, $categoriesWithDirectApproval)) {

			$approvers = DB::table('sap_sync')->where('employee_number', $employee_number)->first();

			if (!$approvers) {
				Session::flash('error', 'No eligible approvers found.');
				return redirect($data['forwardingURL'] . '/' . 'details' . '/' . $id);
			}
			//Fetch the Employee's Line Manager for Approval
			$line_manager_id = Employee::where('employee_number', $approvers->line_manager_id)->first();

			if ($maxApprovalLevel == 0 || $maxApprovalLevel == null) {
				if ($category->category_name == "Fuel - Receipts") {
					$voucherItems = DB::table('voucher_items')
						->where('voucher_id', '=', $voucher->id)
						->where('category_id', '=', $category->id)
						->get();

					foreach ($voucherItems as $item) {
						if ($item->official_travel == "Yes") {
							$data['client'] = Employee::find($line_manager_id->id);
							$data['employee'] = Employee::find($employee_id);
							$data['voucher'] = $voucher;

							DB::beginTransaction();

							$voucherApproval = new Voucher_Employee();
							$voucherApproval->voucher_id = $id;
							$voucherApproval->employee_id = $line_manager_id->id;
							$voucherApproval->save();

							$voucher->status = 'Submitted';
							$voucher->save();
							DB::commit();

							if (isset($data['client']->email)) {
								if (isset($data['employee']->email)) {
									$mail = new MailController();
									$mail->send($data, $data, $data['forwardingURL'] . '-submitted', $data['panelHeading'] . ' Submitted by ' . $data['employee']->employee_name);
								} else {
									$mail = new MailController();
									$mail->send($data, null, $data['forwardingURL'] . '-submitted', $data['panelHeading'] . ' Submitted by ' . $data['employee']->employee_name);
								}
							}

							Session::flash('success', $data['panelHeading'] . ' sent for approval successfully');
							return redirect($data['forwardingURL'] . '/' . 'details' . '/' . $id);
						}
					}
				}

				$voucher->status = 'Approved - Documents in transit';
				$voucher->save();

				$data['voucher'] = $voucher;

				$mail = new MailController();
				$mail->send($data, $data, $data['forwardingURL'] . '-approved', $data['panelHeading'] . ' Approved');

				Session::flash('success', 'Claim has been submitted and automatically approved.');
				return redirect()->back();
			}

			$approval = Voucher_Employee::where('voucher_id', '=', $id)
				->where('employee_id', '=', $approvers->id)
				->get();

			// dd($approvers);

			// dd($line_manager_id->id);

			if (count($approval) == 0) {
				//Send to Line Manager for Approval
				DB::beginTransaction();
				$voucherApproval = new Voucher_Employee();
				$voucherApproval->voucher_id = $id;
				// if($line_manager_id->employee_number == "10000020")
				// 	$voucherApproval->employee_id = "35";
				// else
				// 	$voucherApproval->employee_id = $line_manager_id->id;
				$voucherApproval->employee_id = $line_manager_id->id;
				$voucherApproval->save();

				$voucher->status = 'Submitted';
				$voucher->save();

				DB::commit();

				// Send email notification logic for the approver...
				$data['client'] = Employee::find($line_manager_id->id);
				$data['employee'] = Employee::find($employee_id);
				$data['voucher'] = $voucher;

				if (isset($data['client']->email)) {
					if (isset($data['employee']->email)) {
						$mail = new MailController();
						$mail->send($data, $data, $data['forwardingURL'] . '-submitted', $data['panelHeading'] . ' Submitted by ' . $data['employee']->employee_name);
					} else {
						$mail = new MailController();
						$mail->send($data, null, $data['forwardingURL'] . '-submitted', $data['panelHeading'] . ' Submitted by ' . $data['employee']->employee_name);
					}
				}

				Session::flash('success', $data['panelHeading'] . ' sent for approval successfully');
				return redirect($data['forwardingURL'] . '/' . 'details' . '/' . $id);
			} else {
				Session::flash('error', 'Already sent for approval.');
				return redirect($data['forwardingURL'] . '/' . 'details' . '/' . $id);
			}
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function approveVoucher($id)
	{
		$employee_id = Session::get('id');
		$employee_number = Session::get('employee_number');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');

		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$voucher = Voucher::find($id);
			$data['employee'] = Employee::find($employee_id);
			if ($data['path'] == 'voucher/approve/{id}') {
				$data['panelHeading'] = 'Claim';
				$data['forwardingURL'] = 'voucher';
				$status = 'Approved - Documents in transit';
			} elseif ($data['path'] == 'travel-order/approve/{id}') {
				$data['panelHeading'] = 'Travel Order';
				$data['forwardingURL'] = 'travel-order';
				$data['client'] = Employee::find(907);
				$data['voucher'] = $voucher;
				$data['lastCountry'] = null;


				$mail = new MailController();
				$mail->send($data, $data, 'travel-order-approved-arrangements', 'Travel Order # ' . $voucher->id . '/' . $voucher->employee->employee_name . '/' . $voucher->employee->employee_number);
			}
			$data['voucher'] = $voucher;
			$data['approval'] = Voucher_Employee::where('employee_id', '=', $data['employee']->id)->where('voucher_id', '=', $voucher->id)->first();

			$maximumLevel = Approval_Level::max('level');

			$categories = $voucher->categories($voucher->id);

			$highestApprovalCategory = null;
			$maxApprovalLevel = 0;

			foreach ($categories as $category) {
				$categoryApprovalLevel = $this->getApprovalLevelForCategory($category->category_name, $voucher);
				if ($categoryApprovalLevel > $maxApprovalLevel) {
					$maxApprovalLevel = $categoryApprovalLevel;
					$highestApprovalCategory = $category->category_name;
				}
			}

			DB::beginTransaction();
			if (in_array($employee_id, [1818,3512]))
		    {
				$data['approval']->approved = 1;
				$data['approval']->save();

				$data['voucher']->status = 'Approved - Documents in transit';
				$data['voucher']->save();

				$mail = new MailController();
				$mail->send($data, $data, $data['forwardingURL'] . '-approved', $data['panelHeading'] . ' Approved');

				DB::commit();
				Session::flash('success', $data['panelHeading'] . ' approved successfully without further approval');
				return redirect('vouchers/require/approval');
			}
			
			$bu = DB::table('sap_sync')->where('employee_number', $employee_number)->first();
			$buHead = DB::table('bu_head')->where('bu_id', $bu->business_unit)->first();
// 			dd($bu);
			//Fetch the Travel Manager
			$travel_manager = Employee::where('custom_view', 'travel-accounts')->first();
			// Automatically approve if the claim is from the MD (IDs: 34) or check if the claim if of the BU Head
// 			if (in_array($voucher->employee_id, [4092]) || $buHead->employee_number == $voucher->employee->employee_number) {
            if (in_array($voucher->employee_id, [4092]) || 
                ($buHead && $voucher->employee_id && $buHead->employee_number == $voucher->employee->employee_number))
		    {
				$data['approval']->approved = 1;
				$data['approval']->save();

				$data['voucher']->status = 'Approved - Documents in transit';
				$data['voucher']->save();

				$mail = new MailController();
				$mail->send($data, $data, $data['forwardingURL'] . '-approved', $data['panelHeading'] . ' Approved');

				DB::commit();
				Session::flash('success', $data['panelHeading'] . ' approved successfully without further approval');
				return redirect('vouchers/require/approval');
			}

			if ($buHead) {
				$currentApproverEmployeeNumber = $data['employee']->employee_number;

				//MD is the Approver
				// if ($currentApproverEmployeeNumber == "10000020"){
				// 	$requiresSecondApproval = false;
				// }
				//Travel Manager is the Approver
				// else if ($currentApproverEmployeeNumber == $travel_manager->employee_number){
				// 	$requiresSecondApproval = false;
				// }
				//BU Head is the Approver
				if ($currentApproverEmployeeNumber == $buHead->employee_number) {
					$requiresSecondApproval = false;

					//Incase of "Local - Hotel Stay" also send to Travel Manager for Approval
					if ($highestApprovalCategory == "Local - Hotel Stay" && $maxApprovalLevel >= 2) {
						$status = 'Approved';

						$buHeadEmployee = Employee::where('employee_number', $buHead->employee_number)->first();
						if ($buHeadEmployee) {
							$voucherApproval = new Voucher_Employee();
							$voucherApproval->voucher_id = $voucher->id;
							$voucherApproval->employee_id = $travel_manager->id;
							$voucherApproval->save();
						}
					}

					if (($highestApprovalCategory == "Local - DA (via Air)") && $maxApprovalLevel >= 2 && $data['voucher']->is_travel_order == 1) {
						$status = 'Approved';

						$buHeadEmployee = Employee::where('employee_number', $buHead->employee_number)->first();
						if ($buHeadEmployee) {
							$voucherApproval = new Voucher_Employee();
							$voucherApproval->voucher_id = $voucher->id;
							$voucherApproval->employee_id = $travel_manager->id;
							$voucherApproval->save();
						}
					}

					//Incase of "Foreign - DA" also send to Travel Manager for Approval after MD's Approval
					if ($highestApprovalCategory == "Foreign - DA" && $maxApprovalLevel >= 2 && $currentApproverEmployeeNumber == "10000020" && $data['voucher']->is_travel_order == 1) {
					     $status = 'Approved';
                        if ($highestApprovalCategory == "Foreign - DA" && $maxApprovalLevel > 2) {
    					    $voucherApproval = new Voucher_Employee();
    						$voucherApproval->voucher_id = $voucher->id;
    						$voucherApproval->employee_id = $travel_manager->id;
    						$voucherApproval->save();
    					} else {
    						$requiresSecondApproval = false;
    						$status = 'Approved - Documents in transit';
    					}
					}
					
					//Incase of "Foreign - DA" also send to MD for Approval and current approver is not the MD
					if ($highestApprovalCategory == "Foreign - DA" && $maxApprovalLevel >= 2 && $currentApproverEmployeeNumber != "10000020") {
					    $status = 'Approved';

						$buHeadEmployee = Employee::where('employee_number', $buHead->employee_number)->first();
						if ($buHeadEmployee) {
							$voucherApproval = new Voucher_Employee();
							$voucherApproval->voucher_id = $voucher->id;
							$voucherApproval->employee_id = "4092";
							$voucherApproval->save();
						}
					}

					//In case of TriPack's Meal Entertainment Category if it exceeds the limit then the expense will be sent to CEO for Approval as well
					if ($highestApprovalCategory == "Meal - Entertainment" && $voucher->employee->department->BusinessUnit->company->id == 1400) {
						$employee_bu = $data['employee']->department->BusinessUnit->id;
						$max_limit = 0;
						if ($employee_bu == 10000038) {
							$max_limit = 50000;
						} else if ($employee_bu == 10000043) {
							$max_limit = 25000;
						} else if ($employee_bu == 10000042) {
							$max_limit = 25000;
						} else if ($employee_bu == 10000041) {
							$max_limit = 25000;
						} else if ($employee_bu == 10000037) {
							$max_limit = 50000;
						} else if ($employee_bu == 10000039) {
							$max_limit = 25000;
						} else if ($employee_bu == 10000036) {
							$max_limit = 200000;
						}

						$voucherItem = DB::table('voucher_items')
							->where('voucher_id', '=', $voucher->id)
							->where('category_id', '=', 236)
							->first();
						if ($voucherItem->amount > $max_limit) {
							$status = 'Approved';

							$voucherApproval = new Voucher_Employee();
							$voucherApproval->voucher_id = $voucher->id;
							$voucherApproval->employee_id = 2773;
							$voucherApproval->save();
						}
					}
				}
				// MD is the Approver
				else if ($currentApproverEmployeeNumber == "10000020") {
					$status = 'Approved';

					if ($highestApprovalCategory == "Foreign - DA" && $maxApprovalLevel > 2 && $data['voucher']->is_travel_order == 1) {
						$voucherApproval = new Voucher_Employee();
						$voucherApproval->voucher_id = $voucher->id;
						$voucherApproval->employee_id = $travel_manager->id;
						$voucherApproval->save();
					} else {
						$requiresSecondApproval = false;
						$status = 'Approved - Documents in transit';
					}
				}
				// Travel Manager is the Approver
				else if ($currentApproverEmployeeNumber == $travel_manager->employee_number) {
					$requiresSecondApproval = false;
					$status = 'Approved - Documents in transit';
				}
				// Line Manager is the Approver 
				else {
					if ($maxApprovalLevel > 1) {
						// 		dump("Here");
						$requiresSecondApproval = true;
						$status = 'Approved';

						//After the Line Manager Approvers, fetch the BU head and send them for Approval if Approval steps > 1
						$buHeadEmployee = Employee::where('employee_number', $buHead->employee_number)->first();
						if ($buHeadEmployee) {
							$voucherApproval = new Voucher_Employee();
							$voucherApproval->voucher_id = $voucher->id;
							$voucherApproval->employee_id = $buHeadEmployee->id;
							$voucherApproval->save();
						}
					} else {
						$requiresSecondApproval = false;
						$status = 'Approved - Documents in transit';
					}
				}
			}


			$data['approval']->approved = 1;
			$data['approval']->save();

			$data['voucher']->status = $status;
			$data['voucher']->save();

			$data['client'] = Employee::find($data['voucher']->employee->id);

			$mail = new MailController();
			$mail->send($data, $data, $data['forwardingURL'] . '-approved', $data['panelHeading'] . ' Approved');

			DB::commit();
			Session::flash('success', $data['panelHeading'] . ' approved successfully');
			return redirect('vouchers/require/approval');
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function secondVoucherApproval(Request $request, $id)
	{
		$employee_id = Session::get('id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			if ($data['path'] == 'voucher/approve/{id}') {
				$data['panelHeading'] = 'Claim';
				$data['forwardingURL'] = 'voucher';
			} elseif ($data['path'] == 'travel-order/approve/{id}') {
				$data['panelHeading'] = 'Travel Order';
				$data['forwardingURL'] = 'travel-order';
			}

			DB::beginTransaction();

			$approval = Voucher_Employee::find($id);
			if (!$approval || $approval->approved) {
				Session::flash('error', 'Voucher has already been approved.');
				return redirect($data['forwardingURL'] . '/details/' . $approval->voucher_id);
			}

			$approval->approved = 1;
			$approval->save();

			$voucher = Voucher::find($approval->voucher_id);
			$department = $voucher->department_id ? Department::find($voucher->department_id) : Department::find($voucher->employee->department_id);
			$businessUnit = $department->businessUnit;

			$secondApprover = DB::table('business_unit_employee')
				->join('employees', 'business_unit_employee.employee_id', '=', 'employees.id')
				->join('grade_ranks', 'employees.grade_id', '=', 'grade_ranks.grade_id')
				->where('business_unit_employee.business_unit_id', '=', $businessUnit->id)
				->where('grade_ranks.level', '=', 3)
				->select('employees.id', 'employees.employee_name', 'grade_ranks.rank', 'grade_ranks.level')
				->orderBy('grade_ranks.rank', 'DESC')
				->orderBy('employees.id', 'DESC')
				->first();

			if (!$secondApprover) {
				Session::flash('error', 'No eligible second approvers found.');
				return redirect($data['forwardingURL'] . '/details/' . $approval->voucher_id);
			}

			$voucherApproval = new Voucher_Employee();
			$voucherApproval->voucher_id = $approval->voucher_id;
			$voucherApproval->employee_id = $secondApprover->id;
			$voucherApproval->save();

			$data['client'] = Employee::find($secondApprover->id);
			$data['employee'] = Employee::find($voucher->employee->id);
			$data['approvedBy'] = Employee::find($approval->employee_id);
			$data['voucher'] = $voucher;

			if (isset($data['client']->email)) {
				$mail = new MailController();
				$mail->send($data, $data, $data['forwardingURL'] . '-foreign-approved', $data['panelHeading'] . ' of ' . $data['employee']->employee_name);
			}

			DB::commit();

			Session::flash('success', $data['panelHeading'] . ' sent for second approval successfully.');
			return redirect('vouchers/require/approval');
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}


	public function rejectVoucher(Request $request, $id)
	{
		$employee_id = Session::get('id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['employee'] = Employee::find($employee_id);
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			DB::beginTransaction();
			$approval = Voucher_Employee::where('voucher_id', '=', $id)->where('employee_id', '=', $data['employee']->id)->first();
			if (isset($approval)) {

				$voucher = Voucher::find($id);
				$voucher->status = 'Rejected';
				$voucher->save();

				/*--------------Auto Updating the travel order status if it was linked with the rejected voucher---------------------*/
				$voucherItem = DB::table('voucher_items')
					->select('travel_order_id', 'id')
					->where('voucher_id', '=', $voucher->id)
					->whereNotNull('travel_order_id')
					->get();

				if (count($voucherItem) > 0) {
					DB::table('vouchers')
						->where('id', '=', $voucherItem[0]->travel_order_id)
						->update(['status' => 'Posted']);

					DB::table('voucher_items')
						->where('id', '=', $voucherItem[0]->id)
						->update(['travel_order_id' => 'NULL']);
				}

				$approval->approved = 0;
				$approval->comments = $request->input('rejection_comments');
				$approval->save();

				$data['rejectionComments'] = $request->input('rejection_comments');
				$data['client'] = Employee::find($voucher->employee->id);
				$data['voucher'] = $voucher;
				if ($data['path'] == 'voucher/reject/{id}') {
					$data['panelHeading'] = 'Claim';
					$data['forwardingURL'] = 'voucher';
				} elseif ($data['path'] == 'travel-order/reject/{id}') {
					$data['panelHeading'] = 'Travel Order';
					$data['forwardingURL'] = 'travel-order';
				}
				$mail = new MailController();
				$mail->send($data, $data, $data['forwardingURL'] . '-rejected', 'Voucher #' . $voucher->id . ' Rejected');
			}
			DB::commit();
			Session::flash('success', 'claim rejected successfuly');
			return redirect('dashboard');
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function sendApproval($id)
	{
		// 		dd("Here");
		$employee_id = Session::get('id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['voucher'] = Voucher::find($id);
			$voucher_item = DB::table("voucher_items")
				->where('voucher_id', $data['voucher']->id)
				->first();
			$voucher_item->category = $data['voucher']->categories($data['voucher']->id)->first();
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			if ($data['path'] == 'voucher/send/approval/{id}') {
				$data['forwardingURL'] = 'voucher';
				$data['panelHeading'] = 'Claim';
			} elseif ($data['path'] == 'travel-order/send/approval/{id}') {
				$data['forwardingURL'] = 'travel-order';
				$data['panelHeading'] = 'Travel Order';
			}
			$officiallyTravelled = 0;
			$alreadySubmitted = Voucher_Employee::where('voucher_id', '=', $id)->first();
			if (isset($alreadySubmitted)) {
				Session::flash('error', 'Submission has already been made for the selected voucher.');
				return Redirect::back();
			}
			$voucher_item_check = Voucher_Item::where('voucher_id', '=', $id)->first();
			// dump($id);
			// dd($voucher_item_check);
			if ($voucher_item_check == null) {
				Session::flash('error', 'No Expense is entered for the selected voucher.');
				return Redirect::back();
			} else {
				//   dd("Here");
				if (count($data['voucher']->categories($data['voucher']->id)) == 1 && $data['voucher']->categories($data['voucher']->id)[0]->category_name == 'Fuel - Receipts') {
					foreach ($data['voucher']->categories($data['voucher']->id) as $category) {
						foreach ($data['voucher']->categoryItems($data['voucher']->id, $category->id) as $voucherItem) {
							if ($voucherItem->official_travel == 'Yes') {
								$officiallyTravelled = 1;
							}
						}
					}
					if ($officiallyTravelled == 1) {
						goto processNormally;
					} else {
						$data['voucher']->status = 'Approved - Documents in transit';
						$voucherApproval = new Voucher_Employee();
						$voucherApproval->voucher_id = $id;
						$voucherApproval->employee_id = $data['employee']->id;
						$voucherApproval->approved = 1;
						$voucherApproval->save();
						$data['voucher']->save();

						Session::flash('success', 'Voucher has been approved. Please send the documents to accounts department');
						return redirect('voucher/details' . '/' . $data['voucher']->id);
					}
				} elseif (count($data['voucher']->categories($data['voucher']->id)) == 1 && $data['voucher']->categories($data['voucher']->id)[0]->category_name == 'Medicals – Mother' || $data['voucher']->categories($data['voucher']->id)[0]->category_name == 'Mobile - Handset') {
					$data['voucher']->status = 'Approved - Documents in transit';
					$voucherApproval = new Voucher_Employee();
					$voucherApproval->voucher_id = $id;
					$voucherApproval->employee_id = $data['employee']->id;
					$voucherApproval->approved = 1;
					$voucherApproval->save();
					$data['voucher']->save();

					Session::flash('success', 'Voucher has been approved. Please send the documents to accounts department');
					return redirect('voucher/details' . '/' . $data['voucher']->id);
				} elseif (count($data['voucher']->categories($data['voucher']->id)) == 1 && $data['voucher']->categories($data['voucher']->id)[0]->category_name == 'Motor Car income tax claim') {
					$data['voucher']->status = 'Approved - Documents in transit';
					$voucherApproval = new Voucher_Employee();
					$voucherApproval->voucher_id = $id;
					$voucherApproval->employee_id = $data['employee']->id;
					$voucherApproval->approved = 1;
					$voucherApproval->save();
					$data['voucher']->save();
					Session::flash('success', 'Voucher has been approved. Please send the documents to accounts department');
					return redirect('voucher/details' . '/' . $data['voucher']->id);
				} else {
					processNormally:
					// dd("Here1");
					if (count($data['voucher']->requiresSecondApproval()) > 0) {
						// dd("Here2");
						$data['approvers'] = $this->twoLevelApproval($data['voucher'], $data['voucher']->employee->level);
					} else {
						// dd("Here3");
						$data['approvers'] = $this->getApprovers($data['voucher'], $data['voucher']->employee->level + 1);
						// dd($data['approvers']);
						$data['voucher']->voucher_item = $voucher_item;
					}
					$data['customLayout'] = 'custom-' . $data['employee']->custom_view . '.layout.ers-layout';
					return view('basic.first-approval', $data);
				}
			}
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	// 	public function getApprovers($voucher, $level)
	// 	{
	// 	    $data['photo'] = Session::get('photo');
	// 		$data['photo_mimetype'] = Session::get('photo_mimetype');

	// 		if ($voucher->department_id == null || $voucher->department_id == '') {
	// 		    $data['department'] = Department::where('id', '=', $voucher->employee->department->id)->first();
	// 		} else {
	// 			$data['department'] = Department::find($voucher->department->id);
	// 		}
	// // 		dump($data['department']->businessUnit);
	// 		$data['approvers'] = $data['department']->businessUnit->levelApprovers($level)->get();
	// // 		dd($data['approvers']);
	// 		$maximumLevel = Approval_Level::max('level');
	// 		while (count($data['approvers']) == 0) {
	// 			if ($level == $maximumLevel) {
	// 				$data['approvers'] = $data['department']->businessUnit->levelApprovers($level)->get();
	// 				if (count($data['approvers']) == 0) {
	// 					Session::flash('error', 'There is not approver for your level in the system. Please contact System Administrator');
	// 					break;
	// 				}
	// 			} else {
	// 				$level += 1;
	// 				$data['approvers'] = $data['department']->businessUnit->levelApprovers($level)->get();
	// 			}
	// 		}
	// // 		dd($data['approvers']);
	// 		return $data['approvers'];
	// 	}

	public function getApprovers($voucher)
	{
		$data['voucher'] = $voucher;

		$costCenter = $voucher->charge_to_cost_center;

		if (!$costCenter) {
			Session::flash('error', 'Cost center not found. Please contact System Administrator.');
			return [];
		}

		$cost_center_approvers = DB::table('cost_center_approvers')->where('cost_center', $costCenter)->first();

		if (!$cost_center_approvers) {
			Session::flash('error', 'No approvers found for the given cost center.');
			return [];
		}

		$approverLevels = [
			1 => 'level_1',
			2 => 'level_2',
			3 => 'level_3',
			4 => 'level_4',
			5 => 'level_5'
		];

		$employee_number = $voucher->employee->employee_number;
		$employeeLevel = null;

		foreach ($approverLevels as $level => $column) {
			if (!empty($cost_center_approvers->$column)) {
				$approvers = explode(',', $cost_center_approvers->$column);
				if (in_array($employee_number, $approvers)) {
					$employeeLevel = $level;
					break;
				}
			}
		}
		// dump($employeeLevel);

		$filteredApprovers = [];

		// If employee exists at a level, show only higher levels
		foreach ($approverLevels as $level => $column) {
			if ($employeeLevel === null || $level > $employeeLevel) {
				if (!empty($cost_center_approvers->$column)) {
					$filteredApprovers = array_merge($filteredApprovers, explode(',', $cost_center_approvers->$column));
				}
			}
		}
		// dd($filteredApprovers);
		$Approvers = Employee::WhereIn('employee_number', $filteredApprovers)->get();
		// dd($Approvers);
		return $Approvers;
	}

	public function twoLevelApproval($voucher, $level)
	{
		if ($level == 3 || $level == 4) {
			return $this->getApprovers($voucher, $voucher->employee->level + 1);
		} elseif ($level == 5) {
			return 0;
		} else {
			return $this->getApprovers($voucher, 3);
		}
	}

	public function getExpenseGraph(Request $request)
	{
		$user_id = Session::get('id');
		if (isset($user_id)) {
			$user = Employee::find($user_id);
			$businessUnit = Business_Unit::find($request->input('division'));
			if ($user->level == 4 || $user->level == 3 || $user->level == 5) {
				$data['all_expense'] = DB::table('vouchers')
					->join('voucher_items', 'vouchers.id', '=', 'voucher_items.voucher_id')
					->join('employees', 'employees.id', '=', 'vouchers.employee_id')
					->join('departments', 'departments.id', '=', 'employees.department_id')
					->join('business_units', 'business_units.id', '=', 'departments.business_unit_id')
					->join('categories', 'categories.id', '=', 'voucher_items.category_id')
					->select(DB::Raw('categories.category_name as label , sum(voucher_items.amount) as data, count(voucher_items.id) as count'))
					->whereRaw('business_units.id = ' . "'" . $businessUnit->id . "'")
					->where('status', '=', 'Posted')
					->where('processed_at', '>=', $request->input('from_date'))
					->where('processed_at', '<=', $request->input('to_date'))
					->groupby('label')
					->get();
				$data['chart_title'] = 'Divisional Expense for ' . $businessUnit->bu_name;
				return response(json_encode($data));
			}
		} else {
			return false;
		}
	}

	public function createVoucherItems($id)
	{
		$employee_id = Session::get('id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			// $data['categories'] =Category::with('company')->whereHas('company',function($query) use ($data){
			//     $query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
			// })->orderby('category_name', 'ASC')->get();
			if ($data['employee']->department->businessUnit->company->id == "45") {
				// dd($data['employee']->grade);
				// dd($data['employee']->grade_id);
				if ($data['employee']->grade_id >= "339" && $data['employee']->grade_id <= "343") {
					// dd("Here");
					$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
						$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
					})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('voucher_suitable', '=', 1)->get();
				} else {
					$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
						$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
					})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('voucher_suitable', '=', 1)->where('category_name', '<>', 'Fuel - Mileage (out of city)')->where('category_name', '<>', 'Fuel - Mileage (within city)')->where('category_name', '<>', 'Fuel - Receipts')->get();
				}
			} else {
				$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
					$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
				})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('voucher_suitable', '=', 1)->get();
			}
			$data['type'] = 'voucher';
			$level = $data['employee']->level;
			$businessUnit = Business_Unit::find($data['employee']->department->businessUnit->id);
			$data['approvers'] = $businessUnit->approvers($level);
			$data['departments'] = Department::all();

			$data['voucher'] = Voucher::find($id);
			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
			$data['customLayout'] = 'custom-' . $custom_view . '.layout.ers-layout';

			return view('basic.add-voucher-items', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function storeVoucherItems(Request $request, $id)
	{
		$data['employee'] = Employee::find(Session::get('id'));
		$itemCount = $request->input('itemCount');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');

		DB::beginTransaction();

		$voucher = Voucher::find($id);
		for ($i = 0; $i <= $itemCount; $i++) {
			$category = Category::find($request->input('category_' . $i));
			if (isset($category->category_name)) {
				if ($request->input('amount_' . $i) > 0) {
					$voucherItem = new Voucher_Item();
					$dateRange = $request->input('date_range_' . $i);
					$time = $request->input('time_' . $i);
					if (isset($dateRange)) {
						$dateRange = explode('-', $dateRange);
						$voucherItem->date_from = date('Y-m-d H:i:s', strtotime($dateRange[0]));
						$voucherItem->date_to = date('Y-m-d H:i:s', strtotime($dateRange[1]));
					} else {
						if (isset($time)) {
							$voucherItem->date_from = $request->input('date_' . $i) . ' ' . $request->input('time_' . $i);
						} else {
							$voucherItem->date_from = $request->input('date_' . $i);
						}
					}
					if ($category->category_name == 'Foreign - DA') {
						// 		$url = 'http://data.fixer.io/api/'.date('Y-m-d', strtotime($voucherItem->date_from)).'?access_key=466ea18406c2ed1bbefbd39b783dd005&symbols=USD,PKR';
						// 		$getForexRates = curl_init($url);
						// 		curl_setopt($getForexRates, CURLOPT_RETURNTRANSFER, true);
						// 		$result = json_decode(curl_exec($getForexRates));
						// 		curl_close($getForexRates);




						//                 	$USD = $result->rates->PKR/$result->rates->USD;

						// 		$voucherItem->forex_amount = $request->input('amount_'.$i);
						// 		$voucherItem->amount = $request->input('amount_'.$i)*$USD;
						// 		$voucherItem->cur = 'USD';


						$travel_order = Voucher_Item::where('voucher_id', $request->input('travel_order_' . $i))
							->get();
						if (isset($travel_order)) {
							$conversion_rate = $travel_order[0]->amount / $travel_order[0]->forex_amount;
							$voucherItem->forex_amount = $request->input('amount_' . $i);
							$voucherItem->amount = $request->input('amount_' . $i) * $conversion_rate;
							$voucherItem->cur = 'USD';
						} else {
							$url = 'http://data.fixer.io/api/' . date('Y-m-d', strtotime($voucherItem->date_from)) . '?access_key=466ea18406c2ed1bbefbd39b783dd005&symbols=USD,PKR';

							$getForexRates = curl_init($url);
							curl_setopt($getForexRates, CURLOPT_RETURNTRANSFER, true);
							$result = json_decode(curl_exec($getForexRates));
							curl_close($getForexRates);
							$USD = $result->rates->PKR / $result->rates->USD;

							$voucherItem->forex_amount = $request->input('amount_' . $i);
							$voucherItem->amount = $request->input('amount_' . $i) * $USD;
							$voucherItem->cur = 'USD';
						}
					} else if ($category->category_name == 'Foreign - Expenses') {

						$url = 'http://data.fixer.io/api/' . date('Y-m-d', strtotime($voucherItem->date_from)) . '?access_key=466ea18406c2ed1bbefbd39b783dd005&symbols=' . $request->input('cur_' . $i) . ',PKR';
						$getForexRates = curl_init($url);
						curl_setopt($getForexRates, CURLOPT_RETURNTRANSFER, true);
						$result = json_decode(curl_exec($getForexRates));
						curl_close($getForexRates);






						if ($request->input('cur_' . $i) == 'USD') {
							$conversion = $result->rates->PKR / $result->rates->USD;
						} elseif ($request->input('cur_' . $i) == 'EUR') {
							$conversion = $result->rates->PKR / $result->rates->EUR;
						} elseif ($request->input('cur_' . $i) == 'GBP') {
							$conversion = $result->rates->PKR / $result->rates->GBP;
						} elseif ($request->input('cur_' . $i) == 'CAD') {
							$conversion = $result->rates->PKR / $result->rates->CAD;
						} elseif ($request->input('cur_' . $i) == 'ZAR') {
							$conversion = $result->rates->PKR / $result->rates->ZAR;
						} elseif ($request->input('cur_' . $i) == 'AED') {
							$conversion = $result->rates->PKR / $result->rates->AED;
						} elseif ($request->input('cur_' . $i) == 'SAR') {
							$conversion = $result->rates->PKR / $result->rates->SAR;
						} elseif ($request->input('cur_' . $i) == 'CHF') {
							$conversion = $result->rates->PKR / $result->rates->CHF;
						} elseif ($request->input('cur_' . $i) == 'TRY') {
							$conversion = $result->rates->PKR / $result->rates->TRY;
						} elseif ($request->input('cur_' . $i) == 'CNY') {
							$conversion = $result->rates->PKR / $result->rates->CNY;
						} elseif ($request->input('cur_' . $i) == 'LKR') {
							$conversion = $result->rates->PKR / $result->rates->LKR;
						} elseif ($request->input('cur_' . $i) == 'SEK') {
							$conversion = $result->rates->PKR / $result->rates->SEK;
						} elseif ($request->input('cur_' . $i) == 'SGD') {
							$conversion = $result->rates->PKR / $result->rates->SGD;
						} elseif ($request->input('cur_' . $i) == 'UZS') {
							$conversion = $result->rates->PKR / $result->rates->UZS;
						} elseif ($request->input('cur_' . $i) == 'KZT') {
							$conversion = $result->rates->PKR / $result->rates->KZT;
						} elseif ($request->input('cur_' . $i) == 'KES') {
							$conversion = $result->rates->PKR / $result->rates->KES;
						} elseif ($request->input('cur_' . $i) == 'THB') {
							$conversion = $result->rates->PKR / $result->rates->THB;
						}


						$voucherItem->forex_amount = $request->input('forex_amount_' . $i);
						$voucherItem->amount = $request->input('amount_' . $i) * $conversion;
						$voucherItem->cur = $request->input('cur_' . $i);
					} else {
						$voucherItem->amount = $request->input('amount_' . $i);
						$voucherItem->cur = $request->input('currency');
					}

					$voucherItem->travel_mode = $request->input('mode_of_travel_' . $i);
					$voucherItem->receipt_number = $request->input('recipt_number_' . $i);
					$voucherItem->receipt_date = $request->input('date_' . $i);
					$voucherItem->receipt_copy = $request->input('receipt_provided_' . $i);
					$voucherItem->amount_paid = $request->input('amount_paid_' . $i);
					$voucherItem->description = $request->input('description_' . $i);
					$voucherItem->month = $request->input('month_' . $i);
					$voucherItem->rate_per_litre = $request->input('rate_per_litre_' . $i);
					$voucherItem->litres = $request->input('litres_' . $i);
					$voucherItem->rupees_in_words = $request->input('rupees_in_words_' . $i);
					$voucherItem->accomodation_food = $request->input('accomodation_food_' . $i);
					// 	$voucherItem->evidence = $request->input('evidence_'.$i);
					$voucherItem->purpose = $request->input('purpose_' . $i);
					$voucherItem->receipt = 'No Attachment';
					$voucherItem->vehicle_number = $request->input('vehicle_no_' . $i);
					$voucherItem->vehicle_registration_name = $request->input('vehicle_registration_name_' . $i);
					$voucherItem->official_travel = $request->input('official_travel_' . $i);
					$voucherItem->receipt_number = $request->input('receipt_number_' . $i);
					$voucherItem->kms = $request->input('kms_' . $i);
					$voucherItem->toll_tax = $request->input('toll_tax_' . $i);
					$voucherItem->remarks = $request->input('remarks_' . $i);
					$voucherItem->voucher_id = $voucher->id;
					$voucherItem->category_id = $request->input('category_' . $i);
					$voucherItem->from = $request->input('from_' . $i);
					$voucherItem->to = $request->input('to_' . $i);
					$voucherItem->personal_days = $request->input('personal_days_' . $i);
					$voucherItem->meal_dates = $request->input('meal_dates_' . $i);

					$chargeTo = $voucher->charge_to_cost_center;
					if (isset($chargeTo)) {
						$department = Department::where('cost_center', '=', $chargeTo)->first();
						$glCode = GL_Mapping::where('category_id', '=', $request->input('category_' . $i))->where('classification', '=', $department->classification)->get();
					} else {
						$glCode = GL_Mapping::where('category_id', '=', $request->input('category_' . $i))->where('classification', '=', $data['employee']->department->classification)->get();
					}
					//echo $glCode;
					if (count($glCode) > 0) {
						$voucherItem->gl_code = $glCode[0]->gl_account;
					}
					//echo $voucherItem->litres;
					$voucherItem->save();
				}
			}
		}

		DB::commit();

		Session::flash('success', 'New expenses has been saved.');
		return redirect('voucher/details' . '/' . $voucher->id);
	}

	public function processVoucher(Request $request)
	{
		$employee_id = Session::get('id');
		$employee_number = Session::get('employee_number');
		$employee = \App\Employee::find($employee_id);
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');

		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$employee = $data['employee'];
			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
			$data['customView'] = 'custom-' . $custom_view . '.process-vouchers';
			if ($custom_view == 'group-accounts') {
				// $data['vouchers'] = Voucher::with('department.businessUnit.company')
				//     ->whereHas('department.businessUnit.company', function($query) use ($data){
				//         $query->where('companies.group_name', '=', $data['employee']->department->businessUnit->company->group_name);
				//     })->whereNull('processed_at')
				//     ->where('status', '=', 'In Process')
				//     ->where('is_travel_order', '=', NULL)
				//     ->get();
				if ($employee_id == 2 || $employee_id == 7362) {
					$company = 1000;
				} else if ($employee_id == 1) {
					$company = 1700;
				} else if ($employee_id == 3847 || $employee_id == 7363) {
					$company = 1100;
				} else if ($employee_id == 3848) {
					$company = 1200;
				} else if ($employee_id == 3849) {
					$company = 1300;
				} else if ($employee_id == 3850) {
					$company = 1400;
				} else if ($employee_id == 3851) {
					$company = 1500;
				}
				
			    if($employee_id == 7362 || $employee_id == 7363){
			        $data['vouchers'] = Voucher::join('employees', 'vouchers.employee_id', '=', 'employees.id')
					->join('sap_sync', 'employees.employee_number', '=', 'sap_sync.employee_number')
					->join('companies', 'sap_sync.company', '=', 'companies.id')
					->where('sap_sync.company', '=', $company)
					->whereNull('processed_at')
					->where('vouchers.status', '=', 'In Process')
					->where('is_travel_order', '=', NULL)
					->where('employees.location', 'Karachi')
					->select('vouchers.*', 'companies.company_name')
					->get();
			    } else{
    				$data['vouchers'] = Voucher::join('employees', 'vouchers.employee_id', '=', 'employees.id')
    					->join('sap_sync', 'employees.employee_number', '=', 'sap_sync.employee_number')
    					->join('companies', 'sap_sync.company', '=', 'companies.id')
    					->where('sap_sync.company', '=', $company)
    					->whereNull('processed_at')
    					->where('vouchers.status', '=', 'In Process')
    					->where('is_travel_order', '=', NULL)
    					->select('vouchers.*', 'companies.company_name')
    					->get();
			    }
				// dd($data['vouchers']);
			}
			if ($custom_view == 'company-accounts') {
				$data['vouchers'] = Voucher::with('department.businessUnit.company')
					->whereHas('department.businessUnit.company', function ($query) use ($data) {
						$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
					})
					->whereIn('status', ['In Process'])
					->whereNull('is_travel_order')
					->get();

				/*$data['vouchers'] = Voucher::with('department.businessUnit.company')
																																																																				->whereHas('department.businessUnit.company', function($query) use ($data){
																																																																					$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
																																																																				})->whereNull('processed_at')
																																																																				->where('status', '=', 'In Process')
																																																																				->where('is_travel_order', '=', NULL)
																																																																				->get();*/
			}
			if ($custom_view == 'accounts') {
				$data['vouchers'] = Voucher::whereNull('processed_at')
					->where('status', '=', 'In Process')
					->where('is_travel_order', '=', NULL)
					->get();
			}
			if ($custom_view == 'location-accounts') {
				if ($employee->location == 'Karachi') {
					$data['vouchers'] = \App\Voucher::with('department.businessUnit.company', 'employee')->whereHas('department.businessUnit.company', function ($query) use ($employee) {
						$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);
					})
						->whereHas('employee', function ($query) use ($employee) {
							$query->whereIn('location', ['Karachi']);
						})
						->whereIn('status', ['In Process'])->whereNull('is_travel_order')->get();
				} else {
					$data['vouchers'] = \App\Voucher::with('department.businessUnit.company', 'employee')->whereHas('department.businessUnit.company', function ($query) use ($employee) {
						$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);
					})
						->whereHas('employee', function ($query) use ($employee) {
							$query->whereNotIn('location', ['Karachi']);
						})
						->whereIn('status', ['In Process'])->whereNull('is_travel_order')->get();
				}
				/* $data['vouchers'] = Voucher::with('department.businessUnit.company')
																																																																				->whereHas('department.businessUnit.company', function($query) use ($data){
																																																																					$query->whereIn('companies.id',[1,13]);
																																																																				})
																																																																				->whereHas('employee',function($query) use ($employee){
																																																																					$query->whereIn('location',['Karachi']);
																																																																					})
																																																																				->whereIn('status', ['In Process'])
																																																																				->whereNull('is_travel_order')
																																																																				->get();*/
			}

			$data['gl_failure'] = array();
			//return $data['vouchers'];

			if (count($data['vouchers']) > 0) {
				foreach ($data['vouchers'] as $voucher) {
					foreach ($voucher->voucherItems as $item) {
						if ($item->gl_code == NULL) {
							$item->gl_code = "99999";
							$item->save();
							// 			array_push($data['gl_failure'], $item->id);
						}
					}
				}
			}
			$data['voucherItems'] = Voucher_Item::whereIn('id', $data['gl_failure'])->get();
			// 			if (count($data['voucherItems']) > 0) {
			// return view('basic.gl-accounts-failure', $data);
			// 			} else {
			//   dump($data['vouchers']);
			return view('basic.process-vouchers', $data);
			// 			}
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function processMedicalVoucher(Request $request)
	{
		$employee_id = Session::get('id');
		$employee_number = Session::get('employee_number');
		$employee = \App\Employee::find($employee_id);

		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$employee = $data['employee'];
			/*
																																																			 Add the employee ids of the Companies Medical Officers and respective category here
																																																		 */
			if ($employee_id == 4970) {
				$company = 1000;
				$category_id = [24, 71];
			} else if ($employee_id == 1) {
				$company = 1700;
				$category_id = [102];
			}
			$data['vouchers'] = Voucher::join('employees', 'vouchers.employee_id', '=', 'employees.id')
				->join('sap_sync', 'employees.employee_number', '=', 'sap_sync.employee_number')
				->join('companies', 'sap_sync.company', '=', 'companies.id')
				->where('sap_sync.company', '=', $company)
				->whereNull('processed_at')
				->where('vouchers.status', '=', 'In Process')
				->where('is_travel_order', '=', NULL)
				->whereExists(function ($query) use ($category_id) {
					$query->select(DB::raw(1))
						->from('voucher_items')
						->join('vouchers', 'voucher_items.voucher_id', '=', 'vouchers.id')
						->whereIn('voucher_items.category_id', $category_id);
				})
				->select('vouchers.*', 'companies.company_name')
				->get();

			$data['gl_failure'] = array();

			if (count($data['vouchers']) > 0) {
				foreach ($data['vouchers'] as $voucher) {
					foreach ($voucher->voucherItems as $item) {
						if ($item->gl_code == NULL) {
							$item->gl_code = "99999";
							$item->save();
						}
					}
				}
			}
			$data['voucherItems'] = Voucher_Item::whereIn('id', $data['gl_failure'])->get();
			return view('basic.process-vouchers', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function processTravelOrders()
	{
		$employee_id = Session::get('id');
		$employee = \App\Employee::find($employee_id);
		$employee_number = Session::get('employee_number');

		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
			$data['customView'] = 'custom-' . $custom_view . '.process-vouchers';

			if ($custom_view == 'group-accounts') {
				// $data['vouchers'] = Voucher::with('department.businessUnit.company')
				//     ->whereHas('department.businessUnit.company', function($query) use ($data){
				//         $query->where('companies.group_name', '=', $data['employee']->department->businessUnit->company->group_name);
				//     })->whereNull('processed_at')
				//     ->where('status', '=', 'In Process')
				//     ->where('is_travel_order', '=', 1)
				//     ->get();

				if ($employee_id == 2) {
					$company = 1000;
				} else if ($employee_id == 1) {
					$company = 1700;
				} else if ($employee_id == 3847) {
					$company = 1100;
				} else if ($employee_id == 3848) {
					$company = 1200;
				} else if ($employee_id == 3849) {
					$company = 1300;
				} else if ($employee_id == 3850) {
					$company = 1400;
				} else if ($employee_id == 3851) {
					$company = 1500;
				}
				$data['vouchers'] = Voucher::join('employees', 'vouchers.employee_id', '=', 'employees.id')
					->join('sap_sync', 'employees.employee_number', '=', 'sap_sync.employee_number')
					->join('companies', 'sap_sync.company', '=', 'companies.id')
					->where('sap_sync.company', '=', $company)
					->whereNull('processed_at')
					->where('vouchers.status', '=', 'In Process')
					->where('is_travel_order', '=', '1')
					->select('vouchers.*', 'companies.company_name')
					->get();
			}
			if ($custom_view == 'company-accounts') {
				$data['vouchers'] = Voucher::with('department.businessUnit.company')
					->whereHas('department.businessUnit.company', function ($query) use ($data) {
						$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
					})->whereNull('processed_at')
					->where('status', '=', 'In Process')
					->where('is_travel_order', '=', 1)
					->get();
			}
			if ($custom_view == 'location-accounts') {
				if ($employee->location == 'Karachi') {
					$data['vouchers'] = \App\Voucher::with('department.businessUnit.company', 'employee')->whereHas('department.businessUnit.company', function ($query) use ($employee) {
						$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);
					})
						->whereHas('employee', function ($query) use ($employee) {
							$query->whereIn('location', ['Karachi']);
						})
						->whereNull('processed_at')
						->where('status', '=', 'In Process')
						->where('is_travel_order', '=', 1)
						->get();
				} else {
					$data['vouchers'] = \App\Voucher::with('department.businessUnit.company', 'employee')->whereHas('department.businessUnit.company', function ($query) use ($employee) {
						$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);
					})
						->whereHas('employee', function ($query) use ($employee) {
							$query->whereNotIn('location', ['Karachi']);
						})
						->whereNull('processed_at')
						->where('status', '=', 'In Process')
						->where('is_travel_order', '=', 1)
						->get();
				}
			}
			if ($custom_view == 'accounts') {
				$data['vouchers'] = Voucher::whereNull('processed_at')
					->where('status', '=', 'In Process')
					->where('is_travel_order', '=', 1)
					->get();
			}
			$data['conversion_rate_failure'] = array();
			if (count($data['vouchers']) > 0) {
				foreach ($data['vouchers'] as $voucher) {
					foreach ($voucher->voucherItems as $item) {
						if (isset($item->forex_amount) && $item->forex_amount > 0 && $item->amount == 0 && $item->conversion_rate == 0) {
							array_push($data['conversion_rate_failure'], $item->id);
						}
					}
				}
			}
			$data['voucherItems'] = Voucher_Item::whereIn('id', $data['conversion_rate_failure'])->get();
			if (count($data['voucherItems']) > 0) {
				return view('basic.conversion-rate-failures', $data);
			} else {
				return view('basic.process-travel-orders', $data);
			}
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function storeProcessVoucher(Request $request)
	{
		$employee_id = Session::get('id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
			$data['customView'] = 'custom-' . $custom_view . '.processed-voucher-formats';

			DB::beginTransaction();
// 			dd($request->input('actions'));
            $processedVouchers = [];
            $companyCode = 0;
			foreach ($request->input('actions') as $item) {
				$voucher = Voucher::find($item);
				$data['voucher_employee'] = Employee::find($voucher->employee_id);
				if ($voucher->status == 'In Process') {
					$voucher->status = 'Processed';
					$voucher->processed_at = date('Y-m-d H:i:s');
					$voucher->save();

                    if ($employee_id == 2) $companyCode = 1000;
                    else if ($employee_id == 3847) $companyCode = 1100;
                    else if ($employee_id == 3848) $companyCode = 1200;
                    else if ($employee_id == 3849) $companyCode = 1300;
                    else if ($employee_id == 3850) $companyCode = 1400;
                    else if ($employee_id == 3851) $companyCode = 1500;
                    else if ($employee_id == 1) $companyCode = 1700;
                    foreach ($voucher->voucherItems as $item) {
						// 		$data['company'] = Voucher::join('employees', 'vouchers.employee_id', '=', 'employees.id')
						// 			->join('sap_sync', 'employees.employee_number', '=', 'sap_sync.employee_number')
						// 			->join('companies', 'sap_sync.company', '=', 'companies.id')
						// 			->where('employees.id', '3')
						// 			->whereNull('processed_at')
						//                 		->select('companies.company_name')
						// 			->first();

						$data['company'] = $data['voucher_employee']->department->BusinessUnit->company->company_name;
						$sapProcessed = new Processed_Voucher();
						$sapProcessed->sap_key = 40;
						$sapProcessed->gl_account = $item->gl_code;
						$sapProcessed->company = $data['company'];
						$sapProcessed->litres = $item->litres;
						$sapProcessed->rate_per_litre = $item->rate_per_litre;
						if ($item->approved_amount > 0 && $item->amount > $item->approved_amount)
							$sapProcessed->amount = $item->approved_amount;
						else
							$sapProcessed->amount = $item->amount;
						$item->amount_paid = $item->amount;
						$sapProcessed->order_number = $item->vehicle_number;
						$sapProcessed->cc = $voucher->charge_to_cost_center;
						// $sapProcessed->employee_number = $voucher->employee->employee_number;
						if (strpos($voucher->employee->user_name, '-') !== false) {
							$sapProcessed->employee_number = explode('-', $voucher->employee->user_name)[1];
						} else {
							$sapProcessed->employee_number = $voucher->employee->user_name;
						}
						if ($item->category->category_name == 'Fuel - Receipts') {
							if ($item->official_travel == 'Yes') {
								$sapProcessed->jv_description = $item->category->category_name . ' - Official Travel';
							} elseif ($item->official_travel == 'No') {
								$sapProcessed->jv_description = $item->category->category_name . ' - Not Official';
							}
						} else if (isset($item->description)) {
							$sapProcessed->jv_description = $item->category->category_name . ' - ' . $item->description;
						} else {
							$sapProcessed->jv_description = $item->category->category_name;
						}
						$sapProcessed->voucher_id = $voucher->id;
						$sapProcessed->save();
				// 		if ($employee_id == 2) {
				// 			$companyCode = 1000;
				// 			$apiResponse = $this->postClaimsToFI($sapProcessed, $companyCode);
				// 		} else if ($employee_id == 3847) {
				// 			$companyCode = 1100;
				// 			$apiResponse = $this->postClaimsToFI($sapProcessed, $companyCode);
				// 		} else if ($employee_id == 3848) {
				// 			$companyCode = 1200;
				// 			$apiResponse = $this->postClaimsToFI($sapProcessed, $companyCode);
				// 		} else if ($employee_id == 3849) {
				// 			$companyCode = 1300;
				// 			$apiResponse = $this->postClaimsToFI($sapProcessed, $companyCode);
				// 		} else if ($employee_id == 3850) {
				// 			$companyCode = 1400;
				// 			$apiResponse = $this->postClaimsToFI($sapProcessed, $companyCode);
				// 		} else if ($employee_id == 3851) {
				// 			$companyCode = 1500;
				// 			$apiResponse = $this->postClaimsToFI($sapProcessed, $companyCode);
				// 		}
						$item->save();
                        
                        $processedVouchers[] = $sapProcessed;

					}
				}
				$data['client'] = Employee::find($voucher->employee->id);
				$data['voucher'] = $voucher;

				$mail = new MailController();
				$mail->send($data, null, 'voucher-processed', 'Voucher # ' . $voucher->id . ' Processed');
			}
			if (count($processedVouchers) && $companyCode != 1700) {
			    $apiResponse = $this->postClaimsToFI($processedVouchers, $companyCode);
            }

			DB::commit();

			$data['vouchers'] = Voucher::whereIn('id', $request->input('actions'))->orderby('employee_id')->get();
// 			dd($data['vouchers']);
			$data['bankFormat'] = [];

			if ($data['vouchers'][0]->totalApprovedAmount($voucher->id) > 0 && $data['vouchers'][0]->totalAmount($voucher->id) > $data['vouchers'][0]->totalApprovedAmount($voucher->id)) {
				$amount = $data['vouchers'][0]->totalApprovedAmount($data['vouchers'][0]->id);
			} else {
				$amount = $data['vouchers'][0]->totalAmount($data['vouchers'][0]->id);
			}
			if ($data['employee']->department->businessUnit->company->id != '21') {
				for ($i = 0; $i < count($data['vouchers']); $i++) {
				    if (isset($data['vouchers'][$i + 1])) {
					    if ($data['vouchers'][$i]->employee_id == $data['vouchers'][$i + 1]->employee_id) {
							if ($data['vouchers'][$i + 1]->totalApprovedAmount($voucher->id) > 0 && $data['vouchers'][$i + 1]->totalAmount($voucher->id) > $data['vouchers'][$i + 1]->totalApprovedAmount($voucher->id)) {
								$amount += $data['vouchers'][$i + 1]->totalApprovedAmount($data['vouchers'][$i + 1]->id);
							} else {
								$amount += $data['vouchers'][$i + 1]->totalAmount($data['vouchers'][$i + 1]->id);
							}
						} else {
						    $data['empoyee_bank_details'] = SAP_Sync::where('employee_number', $data['vouchers'][$i]->employee->employee_number)->first();
                            $voucher_id = $data['vouchers'][$i]->id;
                            $processed_voucher = Processed_Voucher::where('voucher_id',$voucher_id)->first();
                            $message = null;
                            $api_status_icon = null;
    						if ($processed_voucher){
    						  //  dd($processed_voucher->api_response);
    						    $apiResponses = json_decode($processed_voucher->api_response, true);
    						  //  dump($apiResponses);
    						    if(isset($apiResponses['root']['Response'])){
    						      //  dump($apiResponses['root']['Response']);
    						       foreach($apiResponses['root']['Response'] as $responseItem){
    						            if($responseItem['EmployeeNumber'] == $data['vouchers'][$i]->employee->employee_number){
    						              //  dd("Here");
    						                if(isset($responseItem['Type'])){
        						                $type = $responseItem['Type'] == 'S' ? 'Success' : 'Error';
        						                $message = $type . '   ' . $responseItem['Message'];
        						                
        						                if ($type == 'Success') {
                                                    // Extract 18-digit document number from the message
                                                    preg_match('/\b(\d{18})\b/', $responseItem['Message'], $matches);
                                                    $docNumber = $matches[1] ? $matches[1] : null;
                                                
                                                    $api_status_icon = '✅' ;
                                                    $message = $docNumber;
                                                } else {
                                                    $api_status_icon = '❌';                                                                                                        
                                                    $message = $responseItem['Message'];
                                                }
    						                }
    						            }  
    						       }
    						   }
    						}
							array_push($data['bankFormat'], [
								"employee_number" => $data['vouchers'][$i]->employee->employee_number,
								"employee_name" => $data['vouchers'][$i]->employee->employee_name,
								// "account_number"=>$data['vouchers'][$i]->employee->bankAccounts[0]->pivot->account_number,
								"account_number" => $data['empoyee_bank_details']->bank_account,
								"amount" => $amount,
								"cnic" => $data['vouchers'][$i]->employee->cnic,
								"filterBank" => $data['empoyee_bank_details']->bank,
								"api_status_icon" => $api_status_icon,
								"api_response" => $message
							]);
							$amount = $data['vouchers'][$i + 1]->totalAmount($data['vouchers'][$i + 1]->id);
						}
					} else {
					    $data['empoyee_bank_details'] = SAP_Sync::where('employee_number', $data['vouchers'][$i]->employee->employee_number)->first();
                        $voucher_id = $data['vouchers'][$i]->id;
                        $processed_voucher = Processed_Voucher::where('voucher_id',$voucher_id)->first();
    					$message = null;
    					$api_status_icon = null;
    					if ($processed_voucher){
    						   $apiResponses = json_decode($processed_voucher->api_response, true);
    						   if(isset($apiResponses['root']['Response'])){
    						       $responses = $apiResponses['root']['Response'];
                                    if (isset($responses['EmployeeNumber'])) {
                                        $responses = [$responses];
                                    }
                                    foreach($responses as $responseItem){
    						           if($responseItem['EmployeeNumber'] == $data['vouchers'][$i]->employee->employee_number){
    						                if(isset($responseItem['Type'])){
        						                $type = $responseItem['Type'] == 'S' ? 'Success' : 'Error';
        						                $message = $type . '   ' . $responseItem['Message'];
        						                
        						                if ($type == 'Success') {
                                                    // Extract 18-digit document number from the message
                                                    preg_match('/\b(\d{18})\b/', $responseItem['Message'], $matches);
                                                    $docNumber = $matches[1] ? $matches[1] : null;
                                                
                                                    $api_status_icon = '✅' ;
                                                    $message = $docNumber;
                                                } else {
                                                    $api_status_icon = '❌';
                                                    $message = $responseItem['Message'];
                                                }

    						                }
    						            }  
    						       }
    						   }
    						}
						array_push($data['bankFormat'], [
							"employee_number" => $data['vouchers'][$i]->employee->employee_number,
							"employee_name" => $data['vouchers'][$i]->employee->employee_name,
							"account_number" => $data['empoyee_bank_details']->bank_account,
							"amount" => $amount,
							"cnic" => $data['vouchers'][$i]->employee->cnic,
							"filterBank" => $data['empoyee_bank_details']->bank,
							"api_status_icon" => $api_status_icon,
							"api_response" => $message
						]);
					}
				}
				$data['totalAmount'] = 0;
				$data['counter'] = 1;


				return view('basic.processed-voucher-formats', $data);
			} else {
				for ($i = 0; $i < count($data['vouchers']); $i++) {
					if (isset($data['vouchers'][$i + 1])) {
						/*	$amount = $data['vouchers'][$i+1]->totalAmount($data['vouchers'][$i+1]->id);*/
						$cost_centre = $data['vouchers'][$i]->charge_to_cost_center;
						array_push($data['bankFormat'], [
							"voucher_number" => $data['vouchers'][$i]->id,
							"employee_number" => $data['vouchers'][$i]->employee->employee_number,
							"amount" => $amount,
							"cost_center" => $cost_centre,
							"voucher_items" => $data['vouchers'][$i]->voucherItems
						]);

						$amount = $data['vouchers'][$i + 1]->totalAmount($data['vouchers'][$i + 1]->id);
					} else {
						$cost_centre = $data['vouchers'][$i]->charge_to_cost_center;
                        
						array_push($data['bankFormat'], [
							"voucher_number" => $data['vouchers'][$i]->id,
							"employee_number" => $data['vouchers'][$i]->employee->employee_number,
							"amount" => $amount,
							"cost_center" => $cost_centre,
							"voucher_items" => $data['vouchers'][$i]->voucherItems
						]);
					}
				}
				//dump($data['bankFormat']);
				$data['totalAmount'] = 0;
				$data['counter'] = 1;

				return view('basic.dic-processed-voucher-format', $data);
			}
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function storeProcessTravelOrders(Request $request)
	{
		$employee_id = Session::get('id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
			$data['customView'] = 'custom-' . $custom_view . '.processed-voucher-formats';
			DB::beginTransaction();
			$processedVouchers = [];
            $companyCode = 0;
			foreach ($request->input('vouchers') as $item) {
				$voucher = Voucher::find($item);
				if ($voucher->status == 'In Process') {
					$data['company'] = Voucher::join('employees', 'vouchers.employee_id', '=', 'employees.id')
						->join('sap_sync', 'employees.employee_number', '=', 'sap_sync.employee_number')
						->join('companies', 'sap_sync.company', '=', 'companies.id')
						->where('employees.id', $voucher->employee_id)
						->whereNull('processed_at')
						->select('companies.company_name')
						->first();
					$voucher->status = 'Posted';
					$voucher->processed_at = date('Y-m-d H:i:s');
					$voucher->save();
					
					if ($employee_id == 2) $companyCode = 1000;
                    else if ($employee_id == 3847) $companyCode = 1100;
                    else if ($employee_id == 3848) $companyCode = 1200;
                    else if ($employee_id == 3849) $companyCode = 1300;
                    else if ($employee_id == 3850) $companyCode = 1400;
                    else if ($employee_id == 3851) $companyCode = 1500;
                    else if ($employee_id == 1) $companyCode = 1700;
                    
					foreach ($voucher->voucherItems as $item) {
						$sapProcessed = new Processed_Voucher();
						$sapProcessed->sap_key = 40;
						$sapProcessed->gl_account = "99999";
						$sapProcessed->company = $data['company']->company_name;
						$sapProcessed->amount = isset($item->amount) ? $item->amount : 0;
						$sapProcessed->cc = $voucher->charge_to_cost_center;
						$sapProcessed->employee_number = $voucher->employee->employee_number;
						$description = $item->category->category_name . ' from ' . $item->date_from . ' to ' . $item->date_to;

						/* echo "1+".$item->amount;
																																																																																																				  echo "2+".$item->forex_amount;
																																																																																																				  return;*/
						$sapProcessed->jv_description = $description;
						$sapProcessed->voucher_id = $voucher->id;
						$sapProcessed->save();
						
						if($sapProcessed->amount > 0)
						    $processedVouchers[] = $sapProcessed;

					}
				}
				$data['client'] = Employee::find($voucher->employee->id);
				$data['voucher'] = $voucher;

				$mail = new MailController();
				$mail->send($data, null, 'travel-order-processed', 'Travel Order # ' . $voucher->id . ' Processed');
				
				if (count($processedVouchers) && $companyCode != 1700) {
			        $apiResponse = $this->postClaimsToFI($processedVouchers, $companyCode);
			     //   dump($apiResponse);
                }

			}

			DB::commit();
			$data['vouchers'] = Voucher::whereIn('id', $request->input('vouchers'))->orderby('employee_id')->get();
			$data['bankFormat'] = [];

			$amount = $data['vouchers'][0]->totalAmount($data['vouchers'][0]->id);
			for ($i = 0; $i < count($data['vouchers']); $i++) {
				if (isset($data['vouchers'][$i + 1])) {
					if ($data['vouchers'][$i]->employee_id == $data['vouchers'][$i + 1]->employee_id) {
						$amount += $data['vouchers'][$i + 1]->totalAmount($data['vouchers'][$i + 1]->id);
					} else {
				// 		$data['employee_bank_details'] = SAP_Sync::where('employee_number', $data['vouchers'][$i]->employee->employee_number)->first();
                            $data['empoyee_bank_details'] = SAP_Sync::where('employee_number', $data['vouchers'][$i]->employee->employee_number)->first();
                            $voucher_id = $data['vouchers'][$i]->id;
                            $processed_voucher = Processed_Voucher::where('voucher_id',$voucher_id)->first();
                            $message = null;
                            $api_status_icon = null;
    						if ($processed_voucher){
    						  //  dd($processed_voucher->api_response);
    						    $apiResponses = json_decode($processed_voucher->api_response, true);
    						  //  dump($apiResponses);
    						    if(isset($apiResponses['root']['Response'])){
    						      //  dump($apiResponses['root']['Response']);
    						       foreach($apiResponses['root']['Response'] as $responseItem){
    						            if($responseItem['EmployeeNumber'] == $data['vouchers'][$i]->employee->employee_number){
    						              //  dd("Here");
    						                if(isset($responseItem['Type'])){
        						                $type = $responseItem['Type'] == 'S' ? 'Success' : 'Error';
        						                $message = $type . '   ' . $responseItem['Message'];
        						                
        						                if ($type == 'Success') {
                                                    // Extract 18-digit document number from the message
                                                    preg_match('/\b(\d{18})\b/', $responseItem['Message'], $matches);
                                                    $docNumber = $matches[1] ? $matches[1] : null;
                                                
                                                    $api_status_icon = '✅' ;
                                                    $message = $docNumber;
                                                } else {
                                                    $api_status_icon = '❌';                                                                                                        
                                                    $message = $responseItem['Message'];
                                                }
    						                }
    						            }  
    						       }
    						   }
    						}
						
						
						array_push($data['bankFormat'], [
							"employee_number" => $data['vouchers'][$i]->employee->employee_number,
							"employee_name" => $data['vouchers'][$i]->employee->employee_name,
							"account_number" => $data['employee_bank_details']->account_number,
							"amount" => $amount,
							"cnic" => $data['vouchers'][$i]->employee->cnic,
							"api_status_icon" => $api_status_icon,
							"api_response" => $message
						]);
						$amount = $data['vouchers'][$i + 1]->totalAmount($data['vouchers'][$i + 1]->id);
					}
				} else {
					$data['employee_bank_details'] = SAP_Sync::where('employee_number', $data['vouchers'][$i]->employee->employee_number)->first();

					array_push($data['bankFormat'], [
						"employee_number" => $data['vouchers'][$i]->employee->employee_number,
						"employee_name" => $data['vouchers'][$i]->employee->employee_name,
						"account_number" => $data['employee_bank_details']->account_number,
						"amount" => $amount,
						"cnic" => $data['vouchers'][$i]->employee->cnic
					]);
				}
			}
			$data['totalAmount'] = 0;
			$data['counter'] = 1;

			return view('basic.processed-voucher-formats', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function deleteVoucherItem($id)
	{
		$employee_id = Session::get('id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$voucherItem = Voucher_Item::find($id);
			// if(count($voucherItem) > 0){
			//     dd("Here");
			//     DB::table('vouchers')
			//         ->where('id','=',$voucherItem[0]->travel_order_id)
			//         ->update(['status' => 'Posted']);
			//   }
			if ($voucherItem->travel_order_id != NULL) {
				$freeTA = Voucher::find($voucherItem->travel_order_id);
				$freeTA->status = 'Posted';
				$freeTA->save();
			}
			$voucher_id = $voucherItem->voucher_id;
			$voucherItem->delete();
			
			// Check if there are any remaining items for the voucher
            $remainingItems = Voucher_Item::where('voucher_id', $voucher_id)->count();
    
            if ($remainingItems == 0) {
                // If no items remain, delete the voucher
                $voucher = Voucher::find($voucher_id);
                if ($voucher) {
                    $voucher->delete();
        			Session::flash('success', 'Claim Deleted Successfuly.');
					return redirect('dashboard');
                }
            }
			Session::flash('success', 'Claim item deleted successfuly');
			return redirect('voucher/details' . '/' . $voucher_id);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function updateStatus()
	{
		$employee_id = Session::get('id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
			$data['customView'] = 'custom-' . $custom_view . '.update-voucher-documents-status';
			$data['view'] = View::make('basic.update-voucher-documents-status', $data)->render();
			return view('basic.display', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	// public function updateStatusIndividual(){
	// 	$employee_id = Session::get('id');
	// //	dump($employee_id);
	// 	if(isset($employee_id)){
	// 		$data['path'] = Route::getFacadeRoot()->current()->uri();
	// 		$data['employee'] = Employee::find($employee_id);
	// 	//	dump($data['employee']);
	//         $custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
	// 		$data['customView'] = 'custom-'.$custom_view.'.update-voucher-documents-status-individual';
	//         	$data['vouchers'] = [];
	// 	if($data['employee']->special_keys) {
	//             $specialKeys = explode(',', $data['employee']->special_keys);
	//           //  dump($specialKeys);
	//             $companies = Company::whereIn('id', $specialKeys)->get();
	//             foreach ($companies as $company) {
	//                 $employeeNumbers = array();
	//                 $employeeIds = array();
	//                 $processedDates = array();

	//                 $employees = Employee::whereHas('department.businessUnit.company', function ($query) use ($company) {
	//                     $query->where('companies.id', '=', $company->id);
	//                 })->get();

	//                 foreach ($employees as $employee) {
	//                     array_push($employeeIds, $employee->id);
	//                 }
	//             }

	// 		$data['vouchers'] = Voucher::select('id')->where('status', '=', 'Approved - Documents in transit')->
	//             whereIn('employee_id', $employeeIds)->get();
	// 	}
	// 		return view('basic.update-voucher-documents-status-individual', $data);
	// 	}
	// 	else{
	// 		Session::flash('error', 'Your session has ended. Please login to continue.');
	// 		return redirect('/');
	// 	}
	// }

	public function updateStatusIndividual()
	{
		$employee_id = Session::get('id');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
			$data['customView'] = 'custom-' . $custom_view . '.update-voucher-documents-status-individual';
			$data['vouchers'] = [];
			$employeeIds = array();
			if ($employee_id == 2 || $employee_id == 7362) {
				$company = 1000;
			} else if ($employee_id == 1) {
				$company = 1700;
			} else if ($employee_id == 3847 || $employee_id == 7362) {
				$company = 1100;
			} else if ($employee_id == 3848) {
				$company = 1200;
			} else if ($employee_id == 3849) {
				$company = 1300;
			} else if ($employee_id == 3850) {
				$company = 1400;
			} else if ($employee_id == 3851) {
				$company = 1500;
			}
			
			if($employee_id == 7362 || $employee_id == 7363){
			    $employees = SAP_Sync::join('employees', 'employees.employee_number', '=', 'sap_sync.employee_number')
    				->where('company', $company)
    				->where('employees.location', 'Karachi')
    				->select('employees.id as employee_id')
    				->get();
			} else{
    			$employees = SAP_Sync::join('employees', 'employees.employee_number', '=', 'sap_sync.employee_number')
    				->where('company', $company)
    				->select('employees.id as employee_id')
    				->get();
			}
			foreach ($employees as $employee)
				array_push($employeeIds, $employee->employee_id);

			$data['vouchers'] = Voucher::select('id')->where('status', '=', 'Approved - Documents in transit')->whereIn('employee_id', $employeeIds)->get();
			// dd($data);
			return view('basic.update-voucher-documents-status-individual', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function getVoucherDetails(Request $request)
	{
		$voucher_id = $request->input('voucher_id');
		$voucher = Voucher::find($voucher_id);
		if (isset($voucher)) {
			if ($voucher->status == 'Approved - Documents in transit') {
				return $this->show($voucher_id);
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	public function documentsReceived($id)
	{
		$employee_id = Session::get('id');
		if (isset($employee_id)) {
			$data['employee'] = Employee::find($employee_id);
			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
			$allowed = [0, 1, 3];
			$types = ['accounts', 'group-accounts', 'company-accounts', 'location-accounts'];
			if (in_array($data['employee']->account_type, $allowed) && in_array($custom_view, $types)) {
				DB::beginTransaction();
				$voucher = Voucher::find($id);
				$voucher->status = 'In Process';
				$voucher->save();
				$data['client'] = Employee::find($voucher->employee->id);
				$data['voucher'] = $voucher;
				$mail = new MailController();
				$mail->send($data, null, 'documents-received', 'Documents Received Voucher # ' . $voucher->id);
				DB::commit();

				Session::flash('success', 'Documents of Voucher #' . $id . ' received successfuly');
				return redirect('vouchers/status/update');
			} else {
				Session::flash('error', 'You are not authorized to perform this action.');
				return Redirect::back();
			}
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function documentsReceivedPost(Request $request)
	{
		$ids = $request->input('vouchers');
		DB::beginTransaction();
		foreach ($ids as $id) {
			$voucher = Voucher::find($id);
			$voucher->status = 'In Process';
			$voucher->save();
			$data['client'] = Employee::find($voucher->employee->id);
			$data['voucher'] = $voucher;
			$mail = new MailController();
			$mail->send($data, null, 'documents-received', 'Documents Received Voucher # ' . $voucher->id);
		}
		DB::commit();
		return json_encode('success', 200);
	}

	public function processedVouchers(Request $request)
	{
		$employee_id = Session::get('id');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
			$data['customView'] = 'custom-' . $custom_view . '.processed-vouchers';
			if ($request->method() == 'GET') {
				return view('basic.processed-vouchers-date', $data);
			} else if ($request->method() == 'POST') {
				// $data['vouchers'] = Voucher::whereNotNull('processed_at')
				// 	->whereRaw('date('.$request->input('filterColumn').') >='. "'".$request->input('from')."'")
				// 	->whereRaw('date('.$request->input('filterColumn').') <='. "'".$request->input('to')."'")
				// 	->where('status', '=', $request->input('filterStatus'))
				// 	->get();
				$data['vouchers'] = Voucher::whereNotNull('processed_at')
					->whereHas('employee.bankAccounts', function ($query) use ($request) {
						$query->where('banks.id', '=', $request->input('filterBank'));
					})
					->whereRaw('date(vouchers.' . $request->input('filterColumn') . ') >=' . "'" . $request->input('from') . "'")
					->whereRaw('date(vouchers.' . $request->input('filterColumn') . ') <=' . "'" . $request->input('to') . "'")
					->where('status', '=', $request->input('filterStatus'))
					->get();

				$data['filterBank'] = $request->input('filterBank');
				$data['filterColumn'] = $request->input('filterColumn');
				$data['filterStatus'] = $request->input('filterStatus');
				$data['from'] = $request->input('from');
				$data['to'] = $request->input('to');
				// dd($data['vouchers']);

				return view('basic.processed-vouchers', $data);
			}
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function documentsObjection($id, Request $request)
	{
		$employee_id = Session::get('id');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);

			$voucher = Voucher::find($id);
			$voucher->status = 'Rejected';
			$voucher->save();

			$data['rejectionComments'] = $request->rejection_comments;
			$data['client'] = Employee::find($voucher->employee->id);
			$data['voucher'] = $voucher;

			$approval = Voucher_Employee::where('employee_id', '=', $data['employee']->id)->where('voucher_id', '=', $id)->first();
			if (isset($approval)) {
				$approval->employee_id = $data['employee']->id;
				$approval->voucher_id = $id;
				$approval->comments = $request->rejection_comments;
				$approval->approved = 0;
				$approval->save();
			} else {
				$voucherEmployee = new Voucher_Employee();
				$voucherEmployee->employee_id = $data['employee']->id;
				$voucherEmployee->voucher_id = $id;
				$voucherEmployee->comments = $request->rejection_comments;
				$voucherEmployee->approved = 0;
				$voucherEmployee->save();
			}
			$mail = new MailController();
			$mail->send($data, $data, 'voucher-rejected', 'Voucher #' . $voucher->id . ' Rejected');

			Session::flash('success', 'Objection raised successfuly.');
			return redirect('vouchers/status/update');
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function rejectedVouchers()
	{
		$employee_id = Session::get('id');

		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
			$data['customView'] = 'custom-' . $custom_view . '.processed-vouchers';

			$data['vouchers'] = Voucher::where('status', '=', 'Rejected')
				->where('submission_date', '>=', '2021-04-01')
				->where('submission_date', '<=', '2021-05-01')
				->get();

			return view('basic.vouchers-date', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function rejectedVouchersSelect(Request $request)
	{
		$employee_id = Session::get('id');

		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');

			$vmmdate = $request->input('vmm_date');
			$vmmendingdate = $request->input('vmm_ending_date');

			$data['customView'] = 'custom-' . $custom_view . '.processed-vouchers';

			$data['vouchers'] = Voucher::where('status', '=', 'Rejected')
				->where('submission_date', '>=', $vmmdate)
				->where('submission_date', '<=', $vmmendingdate)
				->get();

			return view('basic.rejected-vouchers', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function removeRejection($id)
	{
		$employee_id = Session::get('id');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);

			$voucher = Voucher::find($id);
			$voucher->status = 'In Process';
			$voucher->save();

			$voucherEmployee = new Voucher_Employee();
			$voucherEmployee->employee_id = $data['employee']->id;
			$voucherEmployee->voucher_id = $id;
			$voucherEmployee->approved = 1;
			$voucherEmployee->comments = 'Documents Cleared';
			$voucherEmployee->save();

			Session::flash('success', 'Objection removed successfuly.');
			return redirect('rejected/vouchers');
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function generateExportFormats($filterBank, $filterColumn, $from, $to)
	{
		$employee_id = Session::get('id');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
			$data['customView'] = 'custom-' . $custom_view . '.processed-voucher-formats';

			// 			$data['vouchers'] = Voucher::whereNotNull('processed_at')->where('status', '=', 'Processed')
			// 				->whereRaw('date('.$filterColumn.') >='. "'".$from."'")
			// 				->whereRaw('date('.$filterColumn.') <='. "'".$to."'")
			// 				->orderby('employee_id')
			// 				->get();
			$data['vouchers'] = Voucher::whereNotNull('processed_at')->where('status', '=', 'Processed')
				->whereHas('employee.bankAccounts', function ($query) use ($filterBank) {
					$query->where('banks.id', '=', $filterBank);
				})
				->whereRaw('date(' . $filterColumn . ') >=' . "'" . $from . "'")
				->whereRaw('date(' . $filterColumn . ') <=' . "'" . $to . "'")
				->orderby('employee_id')
				->get();
			$data['bankFormat'] = [];
			$data['reimbursementFormat'] = [];
			$amount = $data['vouchers'][0]->totalAmount($data['vouchers'][0]->id);
			for ($i = 0; $i < count($data['vouchers']); $i++) {
				if (isset($data['vouchers'][$i]->employee->bankAccounts[0])) {
					if (isset($data['vouchers'][$i + 1])) {
						if ($data['vouchers'][$i]->employee_id == $data['vouchers'][$i + 1]->employee_id) {
							$amount += $data['vouchers'][$i + 1]->totalAmount($data['vouchers'][$i + 1]->id);
						} else {
							array_push($data['bankFormat'], [
								"employee_number" => $data['vouchers'][$i]->employee->employee_number,
								"employee_name" => $data['vouchers'][$i]->employee->employee_name,
								"account_number" => $data['vouchers'][$i]->employee->bankAccounts[0]->pivot->account_number,
								"amount" => $amount,
								"cnic" => $data['vouchers'][$i]->employee->cnic
							]);
							$amount = $data['vouchers'][$i + 1]->totalAmount($data['vouchers'][$i + 1]->id);
						}
					} else {
						array_push($data['bankFormat'], [
							"employee_number" => $data['vouchers'][$i]->employee->employee_number,
							"employee_name" => $data['vouchers'][$i]->employee->employee_name,
							"account_number" => $data['vouchers'][$i]->employee->bankAccounts[0]->pivot->account_number,
							"amount" => $amount,
							"cnic" => $data['vouchers'][$i]->employee->cnic
						]);
					}
				} else {
					Session::flash('error', 'Bank Account # of Employee # ' . $data['vouchers'][$i]->employee->employee_number . ' not found. Please update');
					return Redirect::back();
				}
			}
			$data['totalAmount'] = 0;
			$data['counter'] = 1;
			$data['filterBank'] = $filterBank;

			return view('basic.processed-voucher-formats', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}
	public function sapProcessed($id, $jVNumber)
	{
		$sapProcessed = Processed_Voucher::find($id);
		if (count($sapProcessed) > 0) {
			$sapProcessed->sap_status = 1;
			$sapProcessed->jv_number = $jVNumber;
			$voucher = Voucher::find($sapProcessed->voucher_id);
			$voucher->status = 'Posted';
			$voucher->save();
			$sapProcessed->save();
			return 1;
		} else {
			return 0;
		}
	}


	public function storeTravelOrderMissingDetails(Request $request)
	{
		for ($i = 0; $i < count($request->input('item')); $i++) {
			$voucherItem = Voucher_Item::find($request->input('item')[$i]);
			$voucherItem->gl_code = $request->input('gl_code')[$i];
			$voucherItem->amount = $request->input('conversion_rate')[$i] * $voucherItem->forex_amount;
			$voucherItem->save();
		}
		return redirect('process/travel-orders');
	}

	// public function storeTravelOrderMissingDetails(Request $request){
	//     foreach($request->input('actions') as $action){
	//         $voucherItem = Voucher_Item::find($action);
	//         $voucherItem->gl_code = $request->input('gl_code');
	//         $voucherItem->amount = $request->input('conversion_rate')*$voucherItem->forex_amount;
	//         dump($voucherItem);
	//     }
	//     dd("Done");
	//     return redirect('process/travel-orders');
	// }

	public function storeGLFailures(Request $request)
	{
		for ($i = 0; $i < count($request->input('item')); $i++) {
			$voucherItem = Voucher_Item::find($request->input('item')[$i]);
			$voucherItem->gl_code = $request->input('gl_code')[$i];
			$voucherItem->save();
		}
		return redirect('process/vouchers');
	}

	public function correctPostings()
	{

		/*$data['voucher'] = Voucher::find(90);
																																	  $data['client'] = Employee::find($data['voucher']->employee->id);
																																	  $data['employee'] = Employee::find(1511);

																																	  return view('emails.voucher-submitted', $data);*/
		/*DB::beginTransaction();
																																	  $approval = Voucher_Employee::find(19);
																																	  $approval->approved = 1;
																																	  $approval->save();

																																	  $voucher = Voucher::find($approval->voucher_id);

																																	  $data['client'] = Employee::find(1714);
																																	  $data['employee'] = Employee::find($voucher->employee->id);

																																	  $data['approvedBy'] = Employee::find($approval->employee_id);
																																	  $data['voucher'] = $voucher;

																																	  if(isset($data['client']->email)){
																																		  $mail = new MailController();
																																		  $mail->send($data, $data, 'voucher-foreign-approved', 'Voucher of '.$data['employee']->employee_name);
																																	  }

																																	  DB::commit();*/

		/*$data['vouchers'] = Voucher::with('processedVouchers')->whereHas('processedVouchers', function($query){
																																		  $query->whereNull('sap_status');
																																	  })->get();

																																	  foreach($data['vouchers'] as $voucher){
																																		  $data['client'] = Employee::find($voucher->employee->id);
																																		  $data['voucher'] = $voucher;

																																		  $mail = new MailController();
																																		  $mail->send($data,null,'voucher-processed','Voucher # '.$voucher->id.' Processed');
																																	  }*/

		/*$sapProcessed = Processed_Voucher::all();
																																	  $i = 0;
																																	  foreach($sapProcessed as $voucher){
																																		  $category_id = Category::where('category_name', '=', $voucher->jv_description)->get();
																																		  $employee_id = Employee::where('employee_number', '=', $voucher->employee_number)->get();
																																		  $amount = $voucher->amount;
																																		  $gl_account = $voucher->gl_account;
																																		  $order_number = $voucher->order_number;
																																		  $litres = $voucher->litres;
																																		  $rate_per_litre = $voucher->rate_per_litre;
																																		  $charge_to_cost_center = $voucher->cc;
																																		  $id = $voucher->voucher_id;

																																		  $item = Voucher_Item::join('vouchers','voucher_items.voucher_id', '=','vouchers.id')
																																			  ->join('employees','vouchers.employee_id','=','employees.id')
																																			  ->where('category_id', '=', $category_id[0]->id)
																																			  ->where('amount', '=', $amount)
																																			  ->where('gl_code', '=', $gl_account)
																																			  ->where('vehicle_number', '=', $order_number)
																																			  ->where('litres', '=', $litres)
																																			  ->where('rate_per_litre', '=', $rate_per_litre)
																																			  ->where('vouchers.charge_to_cost_center', '=', $charge_to_cost_center)
																																			  ->where('vouchers.employee_id', '=', $employee_id[0]->id)
																																			  ->where('vouchers.id', '=', $id)
																																			  ->get();
																																		  if(count($item) == 1){
																																			  if(isset($item[0]->description)){
																																				  $voucher->jv_description = $voucher->jv_description.' - '.$item[0]->description;
																																			  }
																																			  if($voucher->jv_description == 'Fuel - Receipts'){
																																				  if($item[0]->official_travel == 'Yes'){
																																					  $voucher->jv_description = $voucher->jv_description.' - Official Travel';
																																				  }
																																				  elseif($item[0]->official_travel == 'No'){
																																					  $voucher->jv_description = $voucher->jv_description.' - Not Official';
																																				  }
																																			  }
																																			  $voucher->save();
																																		  }
																																	  }
																																	  echo $i;*/
	}

	public function calculateDADays($daFrom, $daTo)
	{
		$daFrom = date('Y-m-d H:i:s', strtotime($daFrom));
		$daTo = date('Y-m-d H:i:s', strtotime($daTo));
		$daDays = 0;

		if (date('Y-m-d', strtotime($daFrom)) == date('Y-m-d', strtotime($daTo))) {
			$timeDiff = date_diff(new \DateTime($daFrom), new \DateTime($daTo))->format('%h');
			if ($timeDiff >= 4 && $timeDiff < 8) {
				$daDays = 0.5;
			} else if ($timeDiff >= 8 && $timeDiff <= 24) {
				$daDays = 1;
			}
		} else {
			$fromEndTime = date('Y-m-d', strtotime($daFrom . ' + 1 day')) . ' 00:00:00';
			$toEndTime = date('Y-m-d', strtotime($daFrom)) . ' 00:00:00';

			$timeDiff = date_diff(new \DateTime($daFrom), new \DateTime($fromEndTime))->format('%h');
			if ($timeDiff >= 4 && $timeDiff < 8) {
				$daDays += 0.5;
			} else if ($timeDiff >= 8 && $timeDiff <= 24) {
				$daDays += 1;
			}

			$timeDiff = date_diff(new \DateTime($daTo), new \DateTime($toEndTime))->format('%h');
			if ($timeDiff >= 4 && $timeDiff < 8) {
				$daDays += 0.5;
			} else if ($timeDiff >= 8 && $timeDiff <= 24) {
				$daDays += 1;
			}
			$daysDiff = date_diff(new \DateTime(date('Y-m-d', strtotime($daFrom))), new \DateTime(date('Y-m-d', strtotime($daTo))))->format('%a');
			$daysDiff = (int) $daysDiff;
			if ($daysDiff > 1) {
				$daDays += ($daysDiff - 1);
			}
		}
		return $daDays;
	}

	public function calculateDays(Request $request)
	{
		$daFrom = date('Y-m-d H:i:s', strtotime($request->input('daFrom')));
		$daTo = date('Y-m-d H:i:s', strtotime($request->input('daTo')));
		$daDays = 0;

		if (date('Y-m-d', strtotime($daFrom)) == date('Y-m-d', strtotime($daTo))) {
			$timeDiff = date_diff(new \DateTime($daFrom), new \DateTime($daTo))->format('%h');
			if ($timeDiff >= 4 && $timeDiff < 8) {
				$daDays = 0.5;
			} else if ($timeDiff >= 8 && $timeDiff <= 24) {
				$daDays = 1;
			}
		} else {
			$fromEndTime = date('Y-m-d', strtotime($daFrom . ' + 1 day')) . ' 00:00:00';
			$toEndTime = date('Y-m-d', strtotime($daFrom)) . ' 00:00:00';

			$timeDiff = date_diff(new \DateTime($daFrom), new \DateTime($fromEndTime))->format('%h');
			if ($timeDiff >= 4 && $timeDiff < 8) {
				$daDays += 0.5;
			} else if ($timeDiff >= 8 && $timeDiff <= 24) {
				$daDays += 1;
			}

			$timeDiff = date_diff(new \DateTime($daTo), new \DateTime($toEndTime))->format('%h');
			if ($timeDiff >= 4 && $timeDiff < 8) {
				$daDays += 0.5;
			} else if ($timeDiff >= 8 && $timeDiff <= 24) {
				$daDays += 1;
			}
			$daysDiff = date_diff(new \DateTime(date('Y-m-d', strtotime($daFrom))), new \DateTime(date('Y-m-d', strtotime($daTo))))->format('%a');
			$daysDiff = (int) $daysDiff;
			if ($daysDiff > 1) {
				$daDays += ($daysDiff - 1);
			}
		}
		return response(json_encode($daDays));
	}

	public static function calculateDaysVoucherCopy($from, $to)
	{
		$daFrom = date('Y-m-d H:i:s', strtotime($from));
		$daTo = date('Y-m-d H:i:s', strtotime($to));
		$daDays = 0;

		if (date('Y-m-d', strtotime($daFrom)) == date('Y-m-d', strtotime($daTo))) {
			$timeDiff = date_diff(new \DateTime($daFrom), new \DateTime($daTo))->format('%h');
			if ($timeDiff >= 4 && $timeDiff < 8) {
				$daDays = 0.5;
			} else if ($timeDiff >= 8 && $timeDiff <= 24) {
				$daDays = 1;
			}
		} else {
			$fromEndTime = date('Y-m-d', strtotime($daFrom . ' + 1 day')) . ' 00:00:00';
			$toEndTime = date('Y-m-d', strtotime($daFrom)) . ' 00:00:00';

			$timeDiff = date_diff(new \DateTime($daFrom), new \DateTime($fromEndTime))->format('%h');
			if ($timeDiff >= 4 && $timeDiff < 8) {
				$daDays += 0.5;
			} else if ($timeDiff >= 8 && $timeDiff <= 24) {
				$daDays += 1;
			}

			$timeDiff = date_diff(new \DateTime($daTo), new \DateTime($toEndTime))->format('%h');
			if ($timeDiff >= 4 && $timeDiff < 8) {
				$daDays += 0.5;
			} else if ($timeDiff >= 8 && $timeDiff <= 24) {
				$daDays += 1;
			}

			$daysDiff = date('d', strtotime($daTo)) - date('d', strtotime($daFrom));
			if ($daysDiff > 1) {
				$daDays += ($daysDiff - 1);
			}
		}
		return $daDays;
	}

	public function makeCopy(Request $request)
	{
		$employee_id = Session::get('id');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$voucher_id = base64_decode($request->input('voucher_id')) / 5;
			$data['voucher'] = Voucher::find($voucher_id);
			$data['company'] = NULL;

			if ($data['path'] == 'make/voucher/copy') {
				$data['panelHeading'] = 'New Claim';
				$data['storingURL'] = 'voucher/add';
				$data['type'] = 'voucher';
				if (date('Y-m-d', strtotime($data['voucher']->submission_date)) <= '2020-06-30') {
					$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
						$query->where('companies.id', '=', $data['voucher']->department->businessUnit->company->id);
					})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('voucher_suitable', '=', 1)->get();

					$data['departments'] = Department::whereHas('businessUnit.company', function ($query) use ($data) {
						$query->where('companies.id', '=', $data['voucher']->department->businessUnit->company->id);
					})->get();

					$data['company'] = $data['voucher']->department->businessUnit->company;
				} elseif (date('Y-m-d', strtotime($data['voucher']->submission_date)) > '2020-06-30') {
					$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
						$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
					})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('voucher_suitable', '=', 1)->get();

					$data['departments'] = Department::whereHas('businessUnit.company', function ($query) use ($data) {
						$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
					})->get();
				}
			} elseif ($data['path'] == 'make/travel-order/copy') {
				$data['panelHeading'] = 'New Travel Order';
				$data['storingURL'] = 'travel-order/add';
				$data['type'] = 'travel-order';
				$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
					$query->where('companies.id', '=', $data['voucher']->department->businessUnit->company->id);
				})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('travel_order_suitable', '=', 1)->get();
			}
			$level = $data['employee']->level;
			$businessUnit = Business_Unit::find($data['employee']->department->businessUnit->id);
			$data['approvers'] = $businessUnit->approvers($level);

			if (isset($data['voucher']->child_id)) {
				Session::flash('error', 'Copy of this ' . $data['panelHeading'] . ' already exist.');
				return Redirect::back();
			} else {
				$data['categoryCount'] = 1;
				$data['itemCount'] = -1;
				return view('basic.add-voucher', $data);
			}
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function testApi()
	{
		$data['client'] = Employee::find(Session::get('id'));
		//return view('emails.testing-email', $data);
		if (isset($data['client']->email)) {
			$mail = new MailController();
			$mail->send($data, null, 'voucher-submitted', 'Testing Mail');
		}
	}

	public function testmail()
	{

		$vouchers = Voucher::with('voucherStatus')->where('status', '=', 'Approved - Documents in transit')->get();

		/*$vouchers = Voucher::where('id','=','12292')->get();*/
		dump($vouchers);
		$data['employee'] = Employee::where('user_name', '=', 'ers-admin')->first();
		foreach ($vouchers as $voucher) {
			$difference = date_diff(date_create(date('Y-m-d')), date_create($voucher->voucherStatus[count($voucher->voucherStatus) - 1]->pivot->updated_at))->format('%a');
			dump($difference);
			//dump($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->approved);
			if ($difference > 30) {
				dump($difference);
				dump($voucher);
				dump("4");
				DB::beginTransaction();
				$voucher->status = 'Rejected';
				$voucher->save();
				$voucherStatus = new Voucher_Employee();
				$voucherStatus->voucher_id = $voucher->id;
				$voucherStatus->employee_id = $data['employee']->id;
				$voucherStatus->approved = 0;
				$voucherStatus->comments = 'Auto rejection - Documents not received within 30 days';
				$voucherStatus->save();
				DB::commit();
				$data['voucher'] = $voucher;
				$data['client'] = Employee::find($voucher->employee->id);
				$data['rejectionComments'] = 'Auto rejection - Documents not received within 30 days';
				dump($data);
				$mail = new MailController();
				$mail->send($data, $data, 'voucher-rejected', 'Voucher #' . $voucher->id . ' Rejected');
			}
		}

		$vouchers = Voucher::with('voucherStatus')->where('status', '=', 'Submitted')->get();
		$data['employee'] = Employee::where('user_name', '=', 'ers-admin')->first();
		foreach ($vouchers as $voucher) {
			dump($voucher);
			$difference = date_diff(date_create(date('Y-m-d')), date_create($voucher->voucherStatus[count($voucher->voucherStatus) - 1]->pivot->created_at))->format('%a');
			dump($difference);
			if ($difference > 30 && !isset($voucher->voucherStatus[count($voucher->voucherStatus) - 1]->pivot->approved)) {
				DB::beginTransaction();
				$voucher->status = 'Rejected';
				$voucher->save();
				$voucherStatus = new Voucher_Employee();
				$voucherStatus->voucher_id = $voucher->id;
				$voucherStatus->employee_id = $data['employee']->id;
				$voucherStatus->approved = 0;
				$voucherStatus->comments = 'Auto rejection - Voucher Not Approved within 30 days';
				$voucherStatus->save();
				DB::commit();
				$data['voucher'] = $voucher;
				$data['client'] = Employee::find($voucher->employee->id);
				$data['rejectionComments'] = 'Auto rejection - Voucher Not Approved within 30 days';
				$mail = new MailController();
				$mail->send($data, $data, 'voucher-rejected', 'Voucher #' . $voucher->id . ' Rejected');
			}

			if ($difference == 30 && !isset($voucher->voucherStatus[count($voucher->voucherStatus) - 1]->pivot->approved)) {
				$data['voucher'] = $voucher;
				$data['client'] = Employee::find($voucher->voucherStatus[count($voucher->voucherStatus) - 1]->pivot->employee_id);
				$data['voucher_employee'] = Employee::find($voucher->employee->id);
				$data['rejectionComments'] = 'Auto Reminder - Voucher Not Approved within 30 days';
				dump("1");
				$mail = new MailController();
				$mail->send($data, NULL, 'voucher-reminder', 'Voucher #' . $voucher->id . ' Reminder');
			}
			if ($difference == 20 && !isset($voucher->voucherStatus[count($voucher->voucherStatus) - 1]->pivot->approved)) {
				$data['voucher'] = $voucher;
				$data['client'] = Employee::find($voucher->voucherStatus[count($voucher->voucherStatus) - 1]->pivot->employee_id);
				$data['voucher_employee'] = Employee::find($voucher->employee->id);
				$data['rejectionComments'] = 'Auto Reminder - Voucher Not Approved within 20 days';
				dump("2");
				$mail = new MailController();
				$mail->send($data, NULL, 'voucher-reminder', 'Voucher #' . $voucher->id . ' Reminder');
			}
			if ($difference == 10 && !isset($voucher->voucherStatus[count($voucher->voucherStatus) - 1]->pivot->approved)) {
				$data['voucher'] = $voucher;
				$data['client'] = Employee::find($voucher->voucherStatus[count($voucher->voucherStatus) - 1]->pivot->employee_id);
				$data['voucher_employee'] = Employee::find($voucher->employee->id);
				$data['rejectionComments'] = 'Auto Reminder - Voucher Not Approved within 10 days';
				// dump($data);
				$mail = new MailController();
				$mail->send($data, NULL, 'voucher-reminder', 'Voucher #' . $voucher->id . ' Reminder');
			}
			//                dump($difference);*/
		}
	}

	public function process_vouchers_dic()
	{
		$employee_id = '1864';
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
			$data['customLayout'] = 'custom-' . $data['employee']->custom_view . '.layout.ers-layout';

			$data['customView'] = 'custom-' . $custom_view . '.dic-processed-voucher-formats';
			$ids = [21500, 21373];
			$data['vouchers'] = Voucher::with('voucherItems', 'voucherItems.category')->whereIn('id', $ids)->orderby('employee_id')->get();
			$data['bankFormat'] = [];

			//     dump($data['vouchers']);
			$amount = $data['vouchers'][0]->totalAmount($data['vouchers'][0]->id);
			//	dump($amount);
			for ($i = 0; $i < count($data['vouchers']); $i++) {
				if (isset($data['vouchers'][$i + 1])) {
					if ($data['vouchers'][$i]->employee_id == $data['vouchers'][$i + 1]->employee_id) {
						$amount += $data['vouchers'][$i + 1]->totalAmount($data['vouchers'][$i + 1]->id);
					} else {
						array_push($data['bankFormat'], [
							"voucher_number" => $data['vouchers'][$i]->id,
							"employee_number" => $data['vouchers'][$i]->employee->employee_number,
							"amount" => $amount,
							"cost_center" => $data['vouchers'][$i]->charge_to_cost_center,
							"voucher_items" => $data['vouchers'][$i]->voucherItems
						]);

						$amount = $data['vouchers'][$i + 1]->totalAmount($data['vouchers'][$i + 1]->id);
					}
				} else {


					array_push($data['bankFormat'], [
						"voucher_number" => $data['vouchers'][$i]->id,
						"employee_number" => $data['vouchers'][$i]->employee->employee_number,
						"amount" => $amount,
						"cost_center" => $data['vouchers'][$i]->charge_to_cost_center,
						"voucher_items" => $data['vouchers'][$i]->voucherItems
					]);
				}
			}
			$data['totalAmount'] = 0;
			$data['counter'] = 1;

			//dump($data['bankFormat']);

			return view('basic.dic-processed-voucher-format', $data);
		}
	}

	public function reject_vouchers_ers()
	{
		$vouchers = Voucher_Employee::where('approved', '=', '0')->orwhere('approved', '!=', '1')->get();
		foreach ($vouchers as $voucher) {
			$same_voucher = Voucher_Employee::where('voucher_id', '=', $voucher->voucher_id)->get();
			foreach ($same_voucher as $v) {
				$v->approved = '0';
				$v->save();
			}
		}
	}

	public function travel_order_report(Request $request)
	{
		$employee_id = Session::get('id');
		$data['path'] = Route::getFacadeRoot()->current()->uri();
		$data['employee'] = Employee::find($employee_id);

		$custom_view = $data['employee']['custom_view'];
		$data['customView'] = 'custom-' . $custom_view . '.travel';
		$data['customLayout'] = 'custom-' . $custom_view . '.layout.ers-layout';


		$data['customFilters'] = array(
			'custom-' . $custom_view . '.filters.companies',
			'custom-' . $custom_view . '.filters.divisions',
			'custom-' . $custom_view . '.filters.departments',
			'custom-' . $custom_view . '.filters.employees-multi',
		);

		$clients = $request->input('employee');
		$departments = $request->input('departments');
		$divisions = $request->input('divisions');
		$company = $request->input('company');

		$data['clientFilters'] = $clients;
		$data['departmentFilters'] = $departments;
		$data['divisionFilters'] = $divisions;
		$data['companyFilter'] = $company;

		if (isset($employee_id)) {
			$data['voucher_item_details'] = Voucher_Item::join('categories', 'voucher_items.category_id', '=', 'categories.id')
				->join('vouchers', 'voucher_items.voucher_id', '=', 'vouchers.id')
				->join('employees', 'vouchers.employee_id', '=', 'employees.id')
				->join('grades', 'employees.grade_id', '=', 'grades.id')
				->join('departments', 'employees.department_id', '=', 'departments.id')
				->join('business_units', 'departments.business_unit_id', '=', 'business_units.id')
				->join('companies', 'business_units.company_id', '=', 'companies.id')
				->whereNotNull('voucher_items.travel_order_id')
				->select(
					'vouchers.id',
					'vouchers.submission_date',
					'vouchers.status',
					'vouchers.processed_at',
					'categories.category_name',
					'voucher_items.date_from',
					'voucher_items.date_to',
					'voucher_items.from',
					'voucher_items.to',
					'voucher_items.advance_amount',
					'voucher_items.amount',
					'voucher_items.cur',
					'voucher_items.purpose',
					'employees.employee_number',
					'employees.employee_name',
					'grades.primary_name',
					'departments.department_name',
					'business_units.bu_name',
					'companies.company_name'
				)
				->where(function ($query) use ($departments) {
					if (count($departments) > 0) {
						$query->whereIn('departments.id', $departments);
					}
				})
				->where(function ($query) use ($divisions) {
					if (count($divisions) > 0) {
						$query->whereIn('business_units.id', $divisions);
					}
				})
				->where(function ($query) use ($company) {
					if (count($company) > 0) {
						$query->where('companies.id', '=', $company);
					}
				})
				->where(function ($query) use ($clients) {
					if (count($clients) > 0) {
						$query->whereIn('employees.id', $clients);
					}
				})
				->orderby('vouchers.id', 'DESC')
				->get();
			return view('custom-travel-accounts.travel-order-report', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function showMedicinesMotherFormVoucher()
	{
		$employee_id = Session::get('id');
		$employee_number = Session::get('employee_number');
		$data['photo'] = Session::get('photo');
		$data['photo_mimetype'] = Session::get('photo_mimetype');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$data['employee_sf_data'] = SAP_Sync::where('employee_number', $employee_number)->first();
			$data['departments'] = Department::whereHas('businessUnit.company', function ($query) use ($data) {
				$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
			})->get();
			$data['panelHeading'] = 'New Medicines Mother Claim';
			$data['storingURL'] = 'voucher/medicines-mother-claim/create';
			$data['type'] = 'voucher';
			$data['categories'] = Category::with('company')->whereHas('company', function ($query) use ($data) {
				$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
			})->orderby('category_name', 'ASC')->where('enabled', '=', 1)->where('voucher_suitable', '=', 1)->where('category_name', 'Medicines - Mother')->first();
			$data['vouchers'] = $data['employee']->unapprovedVouchers;
			if ($data['employee_sf_data']->company == 1400)
				$data['entitlements'] = DB::table('entitlements')
					->where('entitlement_type', 'Medical')
					->where('break_down', 'Others')
					->where('employee_number', $data['employee']->employee_number)
					->selectRaw('SUM(consumed_limit) as consumed_limit, SUM(total_limit) as total_limit')
					->first();
			return view('basic.add-voucher-medicines-mother', $data);
		}
	}

	public function createMedicinesMotherVoucher(Request $request)
	{
		$data['path'] = Route::getFacadeRoot()->current()->uri();
		$data['employee'] = Employee::find(Session::get('id'));
		$data['employee_sync'] = SAP_Sync::where('employee_number', $data['employee']->employee_number)->first();
		$data['isMedicinesMotherCategory'] = true;

		$itemCount = $request->input('itemCount');
		$voucher_id = $request->input('voucher_id');
		DB::beginTransaction();

		$voucher = new Voucher();
		$voucher->submission_date = date('Y-m-d');
		$voucher->status = 'Draft';
		$voucher->employee_id = $data['employee']->id;
		$department = Department::find($request->input('cost_center'));
		$charge_to_cost_center = $data['employee_sync']->cost_center;
		$voucher->charge_to_cost_center = $charge_to_cost_center;
		if ($charge_to_cost_center) {
			if (isset($voucher_id)) {
				$voucher->parent_id = $voucher_id;
				$voucher->save();
				$voucherParent = Voucher::find($voucher_id);
				$voucherParent->child_id = $voucher->id;
				$voucherParent->save();
			} else {
				$voucher->save();
			}
			$category = Category::find($request->input('category_1'));
			if (isset($category->category_name)) {
				if ($request->input('amount_1') > 0) {
					if (isset($category->duplication_check)) {
						if (isset($voucher_id)) {
							if ($voucherParent->status == 'Rejected' && (!isset($voucherParent->child_id) || !isset($voucherParent->parent_id))) {
								goto normalProcess;
							} else {
								goto checkDuplicate;
							}
						}
						checkDuplicate:
						$items = Voucher_Item::join('vouchers', 'vouchers.id', '=', 'voucher_items.voucher_id')
							->where('category_id', '=', $category->id)
							->where('date_from', '=', date('Y-m-d', strtotime($request->input('date_1'))) . ' 00:00:00')
							->where('receipt_date', '=', date('Y-m-d', strtotime($request->input('date_1'))) . ' 00:00:00')
							->where('amount', '=', $request->input('amount_1'))
							->where('vouchers.employee_id', '=', $data['employee']->id)
							->get();
						if (count($items) == 0) {
							normalProcess:
							$voucherItem = new Voucher_Item();
							$dateRange = $request->input('date_range_1');
							$time = $request->input('time_1');
							if (isset($dateRange)) {
								$dateRange = preg_split('/\s-\s/', $dateRange);

								$dateFrom = trim($dateRange[0]);
								$dateTo = trim($dateRange[1]);

								$voucherItem->date_from = date('Y-m-d H:i:s', strtotime($dateFrom));
								$voucherItem->date_to = date('Y-m-d H:i:s', strtotime($dateTo));
							} else {
								if (isset($time)) {
									$voucherItem->date_from = $request->input('date_1') . ' ' . $request->input('time_1');
								} else {
									$voucherItem->date_from = $request->input('date_1');
								}
							}
							$voucherItem->receipt_date = $request->input('date_1');
							$voucherItem->receipt_copy = $request->input('receipt_provided_1');
							$voucherItem->description = $request->input('description_1');
							$voucherItem->amount = (float) $request->input('amount_1');
							$voucherItem->receipt = 'No Attachment';
							$voucherItem->vehicle_number = "ABC-123";
							$voucherItem->voucher_id = $voucher->id;
							$voucherItem->category_id = $request->input('category_1');
							$attachment = $request->file('attachment_1');
							$extensions = array(
								'jpg',
								'png',
								'JPG',
								'PNG',
								'PDF',
								'pdf',
								'ZIP',
								'zip',
								'RAR',
								'rar',
								'docx',
								'DOCX',
								'PPTX',
								'pptx',
								'MP4',
								'mp4',
								'xls',
								'xlsx',
								'XLSX',
								'vcf',
								'jpeg',
								'JPEG'
							);

							global $filename;
							if ($attachment == NULL) {
								$filename = "No File";
							} else if ($attachment) {
								if ($attachment->getClientOriginalName()) {
									if (in_array($attachment->getClientOriginalExtension(), $extensions)) {
										$cleanedFilename = preg_replace('/[^A-Za-z0-9\-.]/', '', $attachment->getClientOriginalName());
										$filename = time() . $cleanedFilename;
										dump($filename);
										$attachment->move('claim-attachments', $filename);
									} else {
										return Redirect::back()
											->withInput();
									}
								}
							}

							$voucherItem->attachment = $filename;
							$chargeTo = $request->input('cost_center');
							if ($chargeTo != '') {
								$department = Department::find($chargeTo);
								$glCode = GL_Mapping::where('category_id', '=', $request->input('category_1'))
									->where('classification', '=', $department->classification)
									->get();
							} else {
								$glCode = GL_Mapping::where('category_id', '=', $request->input('category_1'))
									->where('classification', '=', $data['employee']->department->classification)
									->get();
							}
							if (count($glCode) > 0) {
								$voucherItem->gl_code = $glCode[0]->gl_account;
							}
							$voucherItem->save();
							exclude:
						} else {
							Session::flash('error', 'An expense Item of ' . $category->category_name . ' was a duplicate and thus not entered in the system.');
						}
					} else {
						goto normalProcess;
					}
				} else {
					Session::flash('error', 'An expense Item of ' . $category->category_name . ' was of PKR 0 and thus not saved');
				}
			}
		} else {
			Session::flash('error', 'Wrong Cost Center Selected. Please try again.');
			return Redirect::back();
		}

		DB::commit();

		Session::flash('success', 'Your claim is saved');
		return redirect('voucher/details' . '/' . $voucher->id);
	}

	public function submitMedicinesMotherVoucher(Request $request, $id)
	{
		$employee_id = Session::get('id');
		$data['employee'] = Employee::find($employee_id);
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['panelHeading'] = 'Claim';
			$data['forwardingURL'] = 'voucher';

			$voucher = Voucher::find($id);
			$VoucherAmount = DB::table('voucher_items')
				->where('voucher_id', '=', $voucher->id)
				->first()->amount;
			$medicines_mother_admin_id = DB::table('employees')->where('user_name', 'medicines-mother-admin')->first()->id;
			if (!$medicines_mother_admin_id) {
				Session::flash('error', 'No eligible approvers found.');
				return redirect($data['forwardingURL'] . '/' . 'details' . '/' . $id);
			}
			if ($data['employee']->company == 1400) {
				$data['entitlements'] = DB::table('entitlements')
					->where('entitlement_type', 'Medical')
					->where('break_down', 'Others')
					->where('employee_number', $data['employee']->employee_number)
					->selectRaw('SUM(consumed_limit) as consumed_limit, SUM(total_limit) as total_limit')
					->first();
				if ($data['entitlements']->consumed_limit < $VoucherAmount) {
					Session::flash('error', 'Claim amount can not be greater than consumed amount.');
					return redirect($data['forwardingURL'] . '/' . 'details' . '/' . $id);
				}
			}
			$approval = Voucher_Employee::where('voucher_id', '=', $id)
				->where('employee_id', '=', $medicines_mother_admin_id)
				->get();
			if (count($approval) == 0) {
				// Send email notification logic for the approver...
				$mail = new MailController();
				$mail->send($data, $data, $data['forwardingURL'] . '-submitted', $data['panelHeading'] . ' Submitted by ' . $data['employee']->employee_name);

				//Send to medicines_mother_admin for Approval
				DB::beginTransaction();
				$voucherApproval = new Voucher_Employee();
				$voucherApproval->voucher_id = $id;
				$voucherApproval->employee_id = $medicines_mother_admin_id;
				$voucherApproval->save();

				$voucher->status = 'Submitted';
				$voucher->save();

				DB::commit();

				Session::flash('success', $data['panelHeading'] . ' sent for approval successfully');
				return redirect($data['forwardingURL'] . '/' . 'details' . '/' . $id);
			} else {
				Session::flash('error', 'Already sent for approval.');
				return redirect($data['forwardingURL'] . '/' . 'details' . '/' . $id);
			}
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}
	public function approveMedicinesMotherVoucher(Request $request, $id)
	{
		$loggedInEmployee_id = Session::get('id');
		$employee_number = Session::get('employee_number');
		$approvedAmount = 0;
		if (isset($loggedInEmployee_id)) {

			// get voucher details			
			$voucher = Voucher::find($id);
			$voucherItem = Voucher_Item::where('voucher_id', $id)
				->whereHas('category', function ($query) {
					$query->where('category_name', 'Medicines - Mother');
				})->first();
			$approvedAmount = $request->input('approvedAmountHidden');

			// get voucher owner details
			$voucherOwnerId = $voucher->employee_id;
			$voucherOwner = Employee::find($voucher->employee_id);
			if ($voucherOwner->company == 1400) {
				$data['entitlements'] = DB::table('entitlements')
					->where('entitlement_type', 'Medical')
					->where('break_down', 'Others')
					->where('employee_number', $voucherOwner->employee_number)
					->selectRaw('SUM(consumed_limit) as consumed_limit, SUM(total_limit) as total_limit')
					->first();

				// get voucher approver etails
				$data['employee'] = Employee::find($loggedInEmployee_id);

				if ($approvedAmount != null || $approvedAmount != '') {

					// if approver approved amount is greater than voucher claim amount
					if ($voucherItem->amount < $approvedAmount) {
						Session::flash('error', 'Approved Amount can not be greater than the actual amount');
						return Redirect::back()->withInput();
					}
					// if approved amount is greater than voucher owner consumed_limit

					if ($data['entitlements']->consumed_limit < $voucherItem->amount) {
						Session::flash('error', 'Claim amount can not be greater than consumed amount.');
						return Redirect::back()->withInput();
					}
				}
			}

			$status = 'Approved - Documents in transit';
			$data['panelHeading'] = 'Claim';
			$data['forwardingURL'] = 'voucher/approve-medicines-mother';
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['voucher'] = $voucher;
			$data['approval'] = Voucher_Employee::where('employee_id', '=', $data['employee']->id)->where('voucher_id', '=', $voucher->id)->first();
			$data['approval']->approved = 1;
			$data['approval']->save();

			$data['voucher']->status = $status;
			$data['voucher']->save();

			$voucherItem->amount_paid = $approvedAmount;
			$voucherItem->save();

			if ($voucherOwner->company == 1400) {
				// Updating Consumed Amount in Entitlements Table
				$update_data['consumed_limit'] = $data['entitlements']->consumed_limit + $voucherItem->approved_amount;
				DB::table('entitlements')->where('id', $data['entitlements']->id)->update($update_data);
			}
			$data['client'] = Employee::find($data['voucher']->employee->id);

			$mail = new MailController();
			$mail->send($data, $data, $data['forwardingURL'] . '-approved', $data['panelHeading'] . ' Approved');

			DB::commit();
			Session::flash('success', $data['panelHeading'] . ' approved successfully');
			return redirect('vouchers/require/approval');
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}


	public function approveMedicalOPDVoucher(Request $request, $id)
	{
		$employee_id = Session::get('id');
		$employee_number = Session::get('employee_number');

		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$voucher = Voucher::find($id);
			$voucherItems = Voucher_Item::where('voucher_id', $id)->get();
			$voucher_employee = Employee::find($voucher->employee_id);
			foreach ($voucherItems as $item) {
				//   dump($request->input('approved_opd_amount.' . $item->id));
				$item->approved_amount = $request->input('approved_opd_amount.' . $item->id);
				if ($item->amount < $item->approved_amount) {
					Session::flash('error', 'Approved Amount can not be greater than the actual amount');
					return Redirect::back()->withInput();
				}
				$item->save();

				/*
																																																																				Updating Consumed Amount in Entitlements Table
																																																																			*/
				$description = $item->description;
				$parts = explode(" - ", $description);
				$break_down = end($parts);

				if ($break_down != "Dental" || $break_down != "Optical") {
					$break_down = "Others";
				}

				$data['entitlements'] = DB::table('entitlements')
					->where('entitlement_type', 'Medical')
					->where('break_down', $break_down)
					->where('employee_number', $voucher_employee->employee_number)
					->orderBy('id', 'desc')
					->first();

				$update_data['consumed_limit'] = $data['entitlements']->consumed_limit + $item->approved_amount;
				DB::table('entitlements')->where('id', $data['entitlements']->id)->update($update_data);
			}
			$data['employee'] = Employee::find($employee_id);
			$data['panelHeading'] = 'Claim';
			$data['forwardingURL'] = 'voucher/approve-medical-opd';
			if ($employee_id == "4971")
				$status = 'Approved - Documents in transit';
			else
				$status = 'In Process';
			$data['voucher'] = $voucher;
			$data['approval'] = Voucher_Employee::where('employee_id', '=', $data['employee']->id)->where('voucher_id', '=', $voucher->id)->first();
			$data['approval']->approved = 1;
			$data['approval']->save();

			$data['voucher']->status = $status;
			$data['voucher']->save();

			$data['client'] = Employee::find($data['voucher']->employee->id);

			$mail = new MailController();
			$mail->send($data, $data, $data['forwardingURL'] . '-approved', $data['panelHeading'] . ' Approved');

			DB::commit();
			Session::flash('success', $data['panelHeading'] . ' approved successfully');
			return redirect('vouchers/require/approval');
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}
	
	public function postClaimsToFI($processedVouchers = [], $companyCode)
    {
        $username = env("PRD_CPI_USERNAME");
        $password = env("PRD_CPI_PASSWORD");
    
        if (empty($processedVouchers)) {
            $processedVouchers = DB::table('processed_vouchers as pv')
                ->join('companies as c', 'pv.company', '=', 'c.company_name')
                ->whereNULL('api_response')
                ->select(
                    'c.id as company_id',
                    'pv.*'
                )
                ->get();
        }
    
        $postVouchers = [];
        // dump($processedVouchers);
        foreach ($processedVouchers as $processedVoucher) {
            $voucherCategories = DB::table("voucher_items")
                ->where('voucher_id', $processedVoucher->voucher_id)
                ->get();
    
            foreach ($voucherCategories as $voucherCategory) {
                $categoryName = DB::table('categories')
                    ->where('id', '=', $voucherCategory->category_id)
                    ->first();
    
                $categoryMapping = DB::table('sap_category_code_mappings')
                    ->where('category_name', '=', $categoryName->category_name)
                    ->first();
    
                if (!$categoryMapping) continue;
    
                if (strpos($processedVoucher->jv_description, $categoryName->category_name) !== false) {
                    $cost_center = $processedVoucher->cc ? $processedVoucher->cc : "";
                    $extract_pkgs_from_cost_center = (!empty($cost_center)) ? substr($cost_center, 0, 4) : "";
                    if ($extract_pkgs_from_cost_center == "PKGS") {
                        $cost_center = substr($cost_center, 4);
                    }
    
                    $postVouchers[] = [
                        "CompanyCode" => $companyCode,
                        "EmployeeNumber" => (string) $processedVoucher->employee_number,
                        "CategoryCode" => str_pad((string) $categoryMapping->category_code, 2, "0", STR_PAD_LEFT),
                        "ReceiptNo" => '',
                        "ReceiptDate" => date('Y-m-d', strtotime($processedVoucher->created_at)),
                        // "ReceiptDate" => '2025-04-26',
                        "MedicalClaimType" => '',
                        "VehicleNumber" => (string) $processedVoucher->order_number,
                        "Litres" => (string) $processedVoucher->litres,
                        "RatePerLitre" => (string) $processedVoucher->rate_per_litre,
                        "Amount" => (string) $processedVoucher->amount,
                        "JvNumber" => "",
                        "JvDescription" => (string) $processedVoucher->jv_description,
                        "CostCenter" => (string) $cost_center,
                        "VoucherID" => (string) $processedVoucher->voucher_id,
                        // "CreatedDate" => '2025-04-26'
                        "CreatedDate" => date('Y-m-d', strtotime($processedVoucher->created_at))
                    ];
                    
                    break;
                }
            }
        }
    
        if (empty($postVouchers)) {
            return [
                'success' => false,
                'message' => 'No valid vouchers found for posting.'
            ];
        }
        // Post to SAP API
        $ch = curl_init();
    
        /*
            DEV URL
        */
        // curl_setopt($ch, CURLOPT_URL, "https://integration-suite-q07hbh9w.it-cpi026-rt.cfapps.eu10-002.hana.ondemand.com/http/Claims");
        
        /*
            PRD URL
        */
        curl_setopt($ch, CURLOPT_URL, "https://integration-suite-prd-ud55bnea.it-cpi026-rt.cfapps.eu10-002.hana.ondemand.com/http/Claims");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode("$username:$password"),
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["Data" => $postVouchers]));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
    
        if ($curlError) {
            return [
                'success' => false,
                'error' => $curlError,
            ];
        }
        
        // dump($postVouchers);
        // dump($response);
        // Update api_response in DB for each processed voucher
        foreach ($processedVouchers as $voucher) {
            DB::table("processed_vouchers")
                ->where('id', '=', $voucher->id)
                ->update([
                    'api_response' => $response
                ]);      
        }
    
        return [
            'success' => true,
            'http_code' => $httpCode,
            'response' => $response,
        ];
    }
    
    
}


	
