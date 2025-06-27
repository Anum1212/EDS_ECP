<?php namespace App\Http\Controllers;

use App\Category;
use App\Employee;
use App\Grade;
use App\Http\Controllers\Controller;

use App\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RateController extends Controller {

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
            $custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
            $data['customView'] = 'custom-'.$custom_view.'.rates';
			return view('basic.rates', $data);
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
            $custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
            $data['customView'] = 'custom-'.$custom_view.'.add-rate';
			return view('basic.add-rate', $data);
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
		if(isset($employee_id)){
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$rate = array(
				"rate_from" => "Rate From",
				"category" => "Category",
				"currency" => "Currency",
				"amount" => "Amount"
			);
			$validator = Validator::make($request->all(),
				[
					"rate_from" => "required",
					"category" => "required",
					"currency" => "required",
					"amount" => "required"
				]);
			$validator->setAttributeNames($rate);
			if ($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			}
			else {
				if(count($request->input('grades')) > 0){
					foreach($request->input('grades') as $grade){
						DB::beginTransaction();
						$rate = Rate::where('rate_from', '=', $request->input('rate_from'))
							->where('rate_name', '=', $request->input('category'))
							->where('currency', '=', $request->input('currency'))
							->where('amount', '=', $request->input('amount'))
							->where('grade', '=', $grade)
							->first();
						if(isset($rate)){
							goto rate;
						}
						else{
							$rate = new Rate();
							rate:
							$rate->rate_from = $request->input('rate_from');
							$rate->rate_name = $request->input('category');
							$rate->currency = $request->input('currency');
							$rate->amount = $request->input('amount');
							$rate->grade = $grade;
							$rate->save();
						}
						DB::commit();
					}
				}
				else{
					Session::flash('error', 'Please select at least one grade.');
					return Redirect::back();
				}
				Session::flash('success', 'Rate has been added successfuly');
				return redirect('rates');
			}
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
		//
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

	public function getCategoryRateDA($categoryName, $from){
		$employee = Employee::find(Session::get('id'));
		if(isset($employee)){
			if(isset($employee->is_director) && $categoryName == 'Foreign - DA'){
				if($employee->is_director == 1){
					$rate = Rate::with('rateGrade.company')
                        ->whereHas('rateGrade.company', function($query) use ($employee){
                            $query->where('companies.id', '=', $employee->department->businessUnit->company->id);
                        })->select('amount')
						->where('rate_name', '=', $categoryName)
						->where('rate_from', '<=', $from)
						->where(function($query) use ($from){
							$query->where('rate_to', '>=', $from)
								->orwhereNull('rate_to');
						})->where('grade', '=', 'Grade- S.M')
						->get();
				}
				else{
					goto processNormal;
				}
			}
			else{
				processNormal:
				// dump("here");
				$isGradeDependent = Rate::where('rate_name', '=', $categoryName)->whereNotNull('grade')->get();
				if(count($isGradeDependent) > 0){
					$employee = Employee::find(Session::get('id'));
					$rate = Rate::with('rateGrade.company')
                        ->whereHas('rateGrade.company', function($query) use ($employee){
                            $query->where('companies.id', '=', $employee->department->businessUnit->company->id);
                        })->select('amount')
						->where('rate_name', '=', $categoryName)
						->where('rate_from', '<=', $from)
						->where(function($query) use ($from){
							$query->where('rate_to', '>=', $from)
								->orwhereNull('rate_to');
						})->where('grade', '=', $employee->employeeGrade->primary_name)
						->get();
				}
				else{
				    // dump("here3");
					$rate = Rate::with('rateGrade.company')
                        ->whereHas('rateGrade.company', function($query) use ($employee){
                            $query->where('companies.id', '=', $employee->department->businessUnit->company->id);
                        })->select('amount')
						->where('rate_name', '=', $categoryName)
						->where('rate_from', '<=', $from)
						->where(function($query) use ($from){
							$query->where('rate_to', '>=', $from)
								->orwhereNull('rate_to');
						})
						->get();
				}
			}
			return $rate;
		}
		else{
			return false;
		}
	}

	public function getCategoryRate($categoryName, $from){
		$employee = Employee::find(Session::get('id'));
		if(isset($employee)){
			if(isset($employee->is_director) && $categoryName == 'Foreign - DA'){
				if($employee->is_director == 1){
					$rate = Rate::with('rateGrade.company')
                        ->whereHas('rateGrade.company', function($query) use ($employee){
                            $query->where('companies.id', '=', $employee->department->businessUnit->company->id);
                        })->select('amount')
						->where('rate_name', '=', $categoryName)
						->where('rate_from', '<=', $from)
						->where(function($query) use ($from){
							$query->where('rate_to', '>=', $from)
								->orwhereNull('rate_to');
						})->where('grade', '=', 'Grade- S.M')
						->get();
				}
				else{
					goto processNormal;
				}
			}
			else{
				processNormal:
				$isGradeDependent = Rate::where('rate_name', '=', $categoryName)->whereNotNull('grade')->get();
				if(count($isGradeDependent) > 0){
					$employee = Employee::find(Session::get('id'));
					$rate = Rate::with('rateGrade.company')
                        ->whereHas('rateGrade.company', function($query) use ($employee){
                            $query->where('companies.id', '=', $employee->department->businessUnit->company->id);
                        })->select('amount')
						->where('rate_name', '=', $categoryName)
						->where('rate_from', '<=', $from)
						->where(function($query) use ($from){
							$query->where('rate_to', '>=', $from)
								->orwhereNull('rate_to');
						})->where('grade', '=', $employee->employeeGrade->primary_name)
						->get();
				}
				else{
					$rate = Rate::with('rateGrade.company')
                        ->whereHas('rateGrade.company', function($query) use ($employee){
                            $query->where('companies.id', '=', $employee->department->businessUnit->company->id);
                        })->select('amount')
						->where('rate_name', '=', $categoryName)
						->where('rate_from', '<=', $from)
						->where(function($query) use ($from){
							$query->where('rate_to', '>=', $from)
								->orwhereNull('rate_to');
						})
						->get();
				}
			}
			return json_encode($rate);
		}
		else{
			return false;
		}
	}

	public function forexToLocal($from){
	    $forexDate = $from;
	    if(strtotime($forexDate) > strtotime(date('Y-m-d'))){
            $forexDate = date('Y-m-d');
        }
	    else{
            $forexDate = $from;
        }
        $url = 'http://data.fixer.io/api/'.date('Y-m-d', strtotime($forexDate)).'?access_key=466ea18406c2ed1bbefbd39b783dd005&symbols=USD,PKR';
        $getForexRates = curl_init($url);
        curl_setopt($getForexRates, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($getForexRates));
        curl_close($getForexRates);

        $USD = $result->rates->PKR/$result->rates->USD;
        return json_encode($USD);
    }

	public function calculateDays($from, $to){

	}
}
