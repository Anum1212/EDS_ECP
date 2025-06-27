<?php namespace App\Http\Controllers;

use App\Approval_Level;
use App\Business_Unit;
use App\Company;
use App\Department;
use App\Employee;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BusinessUnitController extends Controller {

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
            $data['customView'] = 'custom-'.$custom_view.'.divisions';
            $data['approvalLevels'] = Approval_Level::all();
			return view('basic.divisions', $data);
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
            $data['customView'] = 'custom-'.$custom_view.'.add-division';
			return view('basic.add-division', $data);
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
			$division = array(
				"company" => "Company",
				"division_name" => "Division Name"
			);
			$validator = Validator::make($request->all(),
				[
					"division_name" => "required|unique:business_units,bu_name"
				]);
			$validator->setAttributeNames($division);
			if ($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			}
			else {
				DB::beginTransaction();
				$division = new Business_Unit();
				$division->bu_name= $request->input('division_name');
				$division->company_id = $request->input('company');
				$division->save();

				if(count($request->input('approvers'))> 0){
					foreach($request->input('approvers') as $approver){
						$division->approvers()->attach($approver);
					}
				}

				DB::commit();

				Session::flash('success', 'Division has been added successfuly');
				return redirect('divisions');
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

}
