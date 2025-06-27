<?php namespace App\Http\Controllers;

use App\Business_Unit;
use App\Company;
use App\Department;
use App\Employee;
use App\Employee_Dependent;
use App\Feedback;
use App\Grade;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class EmployeeController extends Controller {

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
            $data['customView'] = 'custom-'.$custom_view.'.employees';
			return view('basic.employees', $data);
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
            $data['customView'] = 'custom-'.$custom_view.'.add-employee';
			return view('basic.add-employee', $data);
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

			$user = array(
				"id" => "Emp ID",
				"name" => "Name",
				"email" => "Email",
				"nick_name" => "Nick Name",
				"mobile" => "Mobile",
				"date_of_birth" => "Date of Birth",
				"address" => "Address",
				"city" => "City",
				"cnic" => "CNIC",
				"marital_status" => "Marital Status",
				"department" => "Department",
				"designation" => "Designation",
				"grade" => "Grade",
				"date_of_joining" => "Date of Joining",
				"user_name" => "User Name",
				"level" => "Level",
				"custom_view" => "Custom View",
			);
			$validator = Validator::make($request->all(),
				[
					"id" => "required|unique:employees,employee_number",
					"name" => "required",
					"user_name" => "required|unique:employees,user_name",
					"department" => "required",
					"date_of_birth" => "required",
					"marital_status" => "required",
					"designation" => "required",
					"grade" => "required",
					"date_of_joining" => "required",
					"level" => "required",
				]);
			$validator->setAttributeNames($user);
			if ($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			}
			else {
				DB::beginTransaction();
				$user = new Employee();
				$user->employee_number = $request->input('id');
				$user->employee_name = $request->input('name');
				$user->nick_name = $request->input('nick_name');
				$user->designation = $request->input('designation');
				$user->email = $request->input('email');
				$user->mobile = $request->input('mobile');
				$user->grade = $request->input('grade');
				$user->cnic = $request->input('cnic');
				$user->date_of_joining = $request->input('date_of_joining');
				$user->date_of_birth = $request->input('date_of_birth');
				$user->address = $request->input('address');
				$user->marital_status = $request->input('marital_status');
				$user->city = $request->input('city');
				$user->level = $request->input('level');
				$user->user_name = $request->input('user_name');
				$user->department_id = $request->input('department');
				if($request->input('custom_view') == ''){
					$user->custom_view = null;
				}
				else{
					$user->custom_view = $request->input('custom_view');
				}
				$user->password = md5($request->input('password'));
				$user->save();

				if(count($request->input('approver_of'))> 0){
					foreach($request->input('approver_of') as $division){
						$user->divisionsApprover()->attach($division);
					}
				}

				if(count($request->input('vehicle_number'))> 0){
					for($i=0; $i<count($request->input('vehicle_number')); $i++){
						$vehicle = new Vehicle();
						$vehicle->vehicle_number = $request->input('vehicle_number')[$i];
						$vehicle->vehicle_description = $request->input('vehicle_description')[$i];
						$vehicle->employee_id = $user->id;
						$vehicle->save();
					}
				}

				DB::commit();

				Session::flash('success', 'Employee has been added successfuly');
				return redirect('employees');
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
		$employee_id = Session::get('id');
		if(isset($employee_id)){
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
            $custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
            $data['customView'] = 'custom-'.$custom_view.'.update-employee';
			$data['employeeToUpdate'] = Employee::find($id);
			if(count($data['employeeToUpdate']) > 0){
				return view('basic.update-employee', $data);
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
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		$employee_id = Session::get('id');
		if(isset($employee_id)){
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);

			$user = array(
				"id" => "Emp ID",
				"name" => "Name",
				"email" => "Email",
				"nick_name" => "Nick Name",
				"mobile" => "Mobile",
				"date_of_birth" => "Date of Birth",
				"address" => "Address",
				"city" => "City",
				"cnic" => "CNIC",
				"marital_status" => "Marital Status",
				"department" => "Department",
				"designation" => "Designation",
				"grade" => "Grade",
				"date_of_joining" => "Date of Joining",
				"user_name" => "User Name",
				"level" => "Level",
				"custom_view" => "Custom View",
				"is_director" => "Is Director",
			);
			$validator = Validator::make($request->all(),
				[
					"id" => "required",
					"name" => "required",
					"email" => "required|email",
					"user_name" => "required|unique:employees,user_name,$id",
					"department" => "required",
					"mobile" => "required",
					"date_of_birth" => "required",
					"cnic" => "required",
					"marital_status" => "required",
					"designation" => "required",
					"grade" => "required",
					"date_of_joining" => "required",
					"level" => "required",
					"is_director" => "required",
				]);
			$validator->setAttributeNames($user);
			if ($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			}
			else {
				DB::beginTransaction();
				$user = Employee::find($id);
				$user->employee_number = $request->input('id');
				$user->employee_name = $request->input('name');
				$user->nick_name = $request->input('nick_name');
				$user->designation = $request->input('designation');
				$user->email = $request->input('email');
				$user->mobile = $request->input('mobile');
				$user->grade = $request->input('grade');
				$user->cnic = $request->input('cnic');
				$user->date_of_joining = $request->input('date_of_joining');
				$user->date_of_birth = $request->input('date_of_birth');
				$user->address = $request->input('address');
				$user->marital_status = $request->input('marital_status');
				$user->city = $request->input('city');
				$user->level = $request->input('level');
				$user->user_name = $request->input('user_name');
				$user->department_id = $request->input('department');
				if($request->input('is_director') == 'Yes'){
				    $user->is_director = 1;
                }
				else{
				    $user->is_director = null;
                }
				if($request->input('custom_view') == ''){
					$user->custom_view = null;
				}
				else{
					$user->custom_view = $request->input('custom_view');
				}
				$user->save();

				$user->divisionsApprover()->detach();

				if(count($request->input('approver_of'))> 0){
					foreach($request->input('approver_of') as $division){
						$user->divisionsApprover()->attach($division);
					}
				}

				foreach($user->vehicles as $vehicle){
					$vehicle->delete();
				}

				if(count($request->input('vehicle_number'))> 0){
					for($i=0; $i<count($request->input('vehicle_number')); $i++){
						$vehicle = new Vehicle();
						$vehicle->vehicle_number = $request->input('vehicle_number')[$i];
						$vehicle->vehicle_description = $request->input('vehicle_description')[$i];
						$vehicle->employee_id = $user->id;
						$vehicle->save();
					}
				}

				DB::commit();

				Session::flash('success', 'Employee has been updated successfuly');
				return redirect('employees');
			}

		}
		else{
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function profile(){
		$employee_id = Session::get('id');
		if(isset($employee_id)){
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
            $custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
            $data['customView'] = 'custom-'.$custom_view.'.profile';
			return view('basic.profile', $data);
		}
		else{
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function storeProfile(Request $request){
		$employee_id = Session::get('id');
		if(isset($employee_id)){
			$profile = array(
				"nick_name" => "Nick Name",
				"email" => "Email",
				"mobile" => "Contact #"
			);
			$validator = Validator::make($request->all(),
				[
					"nick_name"=>"required",
					"email"=>"required|email",
					"mobile"=>"required|digits:11",
				]);
			$validator->setAttributeNames($profile);
			if ($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			}
			else {
				DB::beginTransaction();
				$user = Employee::find($employee_id);
				$user->nick_name = $request->input('nick_name');
				$user->email = $request->input('email');
				$user->mobile = $request->input('mobile');
				$user->save();
				DB::commit();

				Session::flash('success', 'Your profile has been updated successfuly');
				return redirect('dashboard');

			}
		}
		else{
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
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

	public function feedback(Request $request){
		$employee_id = Session::get('id');
		if(isset($employee_id)){
			$data['employee'] = Employee::find($employee_id);
			$data['client'] = Employee::where('employee_number', '=', 9035)->first();

			$feedback = new Feedback();
			$feedback->message = $request->input('feedback');
			$feedback->employee_id = $data['employee']->id;
			$feedback->save();
			$data['feedback'] = $feedback;
			Mail::send('emails.feedback-submitted', $data, function ($message) use ($data) {
				$message->from('systems.services@packages.com.pk', 'E R S - Employee Reimbursement System');
				$message->to($data['client']->email, $data['client']->employee_name)
					->cc($data['employee']->email, $data['employee']->employee_name)
					->cc('amjad.shaikh@packages.com.pk', 'Amjad Shaikh')
					->cc('nasir.butt@packages.com.pk', 'Nasir Butt')
					->cc('uzair.mazhar@packages.com.pk', 'Uzair Mazhar')
					->subject('Feedback Submitted by '.$data['employee']->employee_name);
			});

			Session::flash('success', 'Thanks for your feedback.');
			return redirect('dashboard');
		}
		else{
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function addPartialVehicle(){
		$view = View::make('basic.partials.add-vehicle')->render();
		$data['row'] = $view;
		return response(json_encode($data), 200);
	}

	public function updateData(){
	    $company = Company::find(1);
	    if($company){
            $employees = Employee::with('department.businessUnit.company')
                ->whereHas('department.businessUnit.company', function($query) use ($company){
                    $query->where('companies.id', '=', $company->id);
                })->get();
            foreach ($employees as $employee){
                $employee->grade;
                $grade = Grade::with('company')
                    ->whereHas('company', function($query) use ($company){
                        $query->where('companies.id', '=', $company->id);
                    })->where('primary_name', '=', $employee->grade)
                    ->first();
                if($grade){
                    $employee->grade_id = $grade->id;
                    $employee->save();
                }
            }
        }
    }

    public function sapUpdateTesting(Request $request){
        $entityBody = file_get_contents('php://input');
        Log::info('---------------SAP Pushed-------------------');
        Log::info($entityBody);
        Log::info('---------------SAP Pushed-------------------');

        $data = explode("\n", $entityBody);
        $employeeData = array();
        $employees = array();
        foreach($data as $row){
            $columns = explode('^', $row);
            foreach($columns as $column) {
                $key = explode("=", $column)[0];
                $value = explode("=", $column)[1];
                array_set($employeeData, $key,$value);
            }
            array_push($employees, $employeeData);
        }
        foreach($employees as $employee){
            $employee_id = $employee['employeeNumber'];
            $employeeCompanyKey = $employee['employeeCompanyKey'];
            $lineManagerCompanyKey = rtrim($employee['lineManagerCompanyKey']);
            $companies = ["3111K1[2['206", "3111K1]13*23R03Q9"];
            Log::info('-----------------------------------');
            Log::info('Processing Employee #: '.$employee['employeeNumber']);
            Log::info('Processing Employee Company Key: '.$employee['employeeCompanyKey']);
            Log::info('-----------------------------------');
            $user = Employee::with('department.businessUnit.company')
                ->whereHas('department.businessUnit.company', function($query) use ($companies){
                    $query->where('company_key', '=', $companies[0]);
                })->where('user_name', '=', $employee_id)
                ->first();
            if(!$user){
                $user = Employee::with('department.businessUnit.company')
                    ->whereHas('department.businessUnit.company', function($query) use ($companies){
                        $query->where('company_key', '=', $companies[1]);
                    })->where('user_name', '=', $employee_id)
                    ->first();
            }
            if($user){
                $grade = Grade::with('company')
                    ->whereHas('company', function ($query) use ($employeeCompanyKey){
                        $query->where('companies.company_key', '=', $employeeCompanyKey);
                    })->where('secondary_name', '=', $employee['grade'])
                    ->first();
                     Log::info('-----------------------------------');
                      Log::info('Processing Employee Grade : '.$grade);
                $cost_center = Department::with('businessUnit.company')
                    ->whereHas('businessUnit.company', function($query) use ($employeeCompanyKey){
                        $query->where('companies.company_key', '=', $employeeCompanyKey);
                    })->where('cost_center', '=', $employee['costCenter'])
                    ->first();
                     Log::info('-----------------------------------');
                     Log::info('Processing Employee Grade : '.$cost_center);
                $lineManager = Employee::with('department.businessUnit.company')
                    ->whereHas('department.businessUnit.company', function($query) use ($lineManagerCompanyKey){
                        $query->where('companies.company_key', '=', $lineManagerCompanyKey);
                    })->where('user_name', '=', $employee['lineManagerID'])
                    ->first();
                if(isset($grade) && isset($cost_center)){
                    try{
                        $user->employee_name = $employee['employeeName'];
                        $user->designation = $employee['designation'];
                        $user->grade_id = $grade->id;
                        $user->cnic = $employee['cnic'];
                        $user->date_of_joining = $employee['dateOfJoining'];
                        $user->date_of_birth = $employee['dateOfBirth'];
                        $user->address = $employee['address'];
                        $user->marital_status = $employee['maritalStatus'];
                        $user->qualification = $employee['qualification'];
                        $user->father_name = $employee['fatherName'];
                        $user->employment_type = $employee['employmentType'];
                        $user->city = $employee['city'];
                        $user->department_id = $cost_center->id;
                        if($lineManager){
                            $user->leader_id = $lineManager->id;
                            Log::info('Setting Line Manager: '.$lineManager->user_name);
                        }
                        else{
                            $user->leader_id = NULL;
                        }
                        $user->save();
                    }
                    catch(\Exception $e){
                        Log::info('------------- START EXCEPTION DURING EMPLOYEE INSERTION -------------');
                        Log::info('Error #: '.$e->getCode());
                        Log::info('Error Description: '.$e->getMessage());
                        Log::info('------------- END EXCEPTION DURING EMPLOYEE INSERTION -------------');
                    }
                }
                else{
                    Log::info('------------- START EXCEPTION DURING EMPLOYEE INSERTION -------------');
                    Log::info('Employee #: '.$employee['employeeNumber']);
                    Log::info('Hint: Cost Center or Grade does not exist');
                    Log::info('------------- END EXCEPTION DURING EMPLOYEE INSERTION -------------');

                }
            }
            else{
                $grade = Grade::with('company')
                    ->whereHas('company', function ($query) use ($employeeCompanyKey){
                        $query->where('companies.company_key', '=', $employeeCompanyKey);
                    })->where('secondary_name', '=', $employee['grade'])
                    ->first();
                $cost_center = Department::with('businessUnit.company')
                    ->whereHas('businessUnit.company', function($query) use ($employeeCompanyKey){
                        $query->where('companies.company_key', '=', $employeeCompanyKey);
                    })->where('cost_center', '=', $employee['costCenter'])
                    ->first();
                $lineManager = Employee::with('department.businessUnit.company')
                    ->whereHas('department.businessUnit.company', function($query) use ($lineManagerCompanyKey){
                        $query->where('companies.company_key', '=', $lineManagerCompanyKey);
                    })->where('user_name', '=', $employee['lineManagerID'])
                    ->first();

                if(isset($grade) && isset($cost_center)){
                    try{
                        $user = new Employee();
                        $user->employee_number = $employee['employeeNumber'];
                        $user->employee_name = $employee['employeeName'];
                        $user->designation = $employee['designation'];
                        $user->grade_id = $grade->id;
                        $user->cnic = $employee['cnic'];
                        $user->date_of_joining = $employee['dateOfJoining'];
                        $user->date_of_birth = $employee['dateOfBirth'];
                        $user->address = $employee['address'];
                        $user->marital_status = $employee['maritalStatus'];
                        $user->qualification = $employee['qualification'];
                        $user->father_name = $employee['fatherName'];
                        $user->employment_type = $employee['employmentType'];
                        $user->city = $employee['city'];
                        $user->department_id = $cost_center->id;
                        $user->account_type = 2;
                        $user->level = 0;
                        $user->user_name = $employee['employeeNumber'];
                        $user->password = md5($employee['employeeNumber'].'123');
                        if(isset($lineManager)){
                            $user->leader_id = $lineManager->id;
                        }
                        else{
                            $user->leader_id = NULL;
                        }
                        $user->save();
                    }
                    catch(\Exception $e){
                        Log::info('------------- START EXCEPTION DURING EMPLOYEE INSERTION -------------');
                        Log::info('Error #: '.$e->getCode());
                        Log::info('Error Description: '.$e->getMessage());
                        Log::info('------------- END EXCEPTION DURING EMPLOYEE INSERTION -------------');
                    }
                }
                else{
                    Log::info('------------- START EXCEPTION DURING EMPLOYEE INSERTION -------------');
                    Log::info('Employee #: '.$employee['employeeNumber']);
                    Log::info('Hint: Cost Center or Grade does not exist');
                    Log::info('------------- END EXCEPTION DURING EMPLOYEE INSERTION -------------');
                }
            }
        }
        return 'Success';
    }

    public function sapUpdate(Request $request){
        $entityBody = file_get_contents('php://input');
        Log::info('---------------SAP Pushed-------------------');
        Log::info($entityBody);
        Log::info('---------------SAP Pushed-------------------');

        $data = explode("\n", $entityBody);
        $employeeData = array();
        $employees = array();
        foreach($data as $row){
            $columns = explode('^', $row);
            foreach($columns as $column) {
                $key = explode("=", $column)[0];
                $value = explode("=", $column)[1];
                array_set($employeeData, $key,$value);
            }
            array_push($employees, $employeeData);
        }
        foreach($employees as $employee){
            $employee_id = $employee['employeeNumber'];
            $employeeCompanyKey = $employee['employeeCompanyKey'];
            $company = Company::where('company_key', '=', $employeeCompanyKey)->first();
            if($company){
                $employeeSiblingCompanyKey = $company->sibling_company_key;
            }
            else{
                $employeeSiblingCompanyKey = NULL;
            }
            $lineManagerCompanyKey = rtrim($employee['lineManagerCompanyKey']);
             Log::info('-----------------------------------');
            Log::info('Processing Employee #: '.$employee['employeeNumber']);
            Log::info('Processing Line Manager Company Key: '.$lineManagerCompanyKey);
//            $companies = ["3111K1[2['206", "3111K1]13*23R03Q9"];
            Log::info('-----------------------------------');
            Log::info('Processing Employee #: '.$employee['employeeNumber']);
            Log::info('Processing Employee Company Key: '.$employee['employeeCompanyKey']);
            Log::info('-----------------------------------');
            $user = Employee::with('department.businessUnit.company')
                ->whereHas('department.businessUnit.company', function($query) use ($employeeCompanyKey){
                    $query->where('company_key', '=', $employeeCompanyKey);
                })->where('user_name', '=', $employee_id)
                ->first();
                

            if(!$user && $employeeSiblingCompanyKey){
                $user = Employee::with('department.businessUnit.company')
                    ->whereHas('department.businessUnit.company', function($query) use ($employeeSiblingCompanyKey){
                        $query->where('company_key', '=', $employeeSiblingCompanyKey);
                    })->where('employee_number', '=', $employee_id)
                    ->first();
                 Log::info('user details '.$user);
            }
            if($user){
                $grade = Grade::with('company')
                    ->whereHas('company', function ($query) use ($employeeCompanyKey){
                        $query->where('companies.company_key', '=', $employeeCompanyKey);
                    })->where('secondary_name', '=', $employee['grade'])
                    ->first();
                    
                    Log::info($grade);
                   
                $cost_center = Department::with('businessUnit.company')
                    ->whereHas('businessUnit.company', function($query) use ($employeeCompanyKey){
                        $query->where('companies.company_key', '=', $employeeCompanyKey);
                    })->where('cost_center', '=', $employee['costCenter'])
                    ->first();
                   
                $lineManager = Employee::with('department.businessUnit.company')
                    ->whereHas('department.businessUnit.company', function($query) use ($lineManagerCompanyKey){
                        $query->where('companies.company_key', '=', $lineManagerCompanyKey);
                    })->where('user_name', '=', $employee['lineManagerID'])
                    ->first();
                 //log::info('line manager : '.$lineManager); 
                 
                //dump($employee[lineManagerID']);    
                if(isset($grade) && isset($cost_center)){
                    try{
                        $user->employee_name = $employee['employeeName'];
                        $user->designation = $employee['designation'];
                        $user->grade_id = $grade->id;
                        $user->cnic = $employee['cnic'];
                        $user->date_of_joining = $employee['dateOfJoining'];
                        $user->date_of_birth = $employee['dateOfBirth'];
                        $user->address = $employee['address'];
                        $user->marital_status = $employee['maritalStatus'];
                        $user->qualification = $employee['qualification'];
                        $user->father_name = $employee['fatherName'];
                        $user->employment_type = $employee['employmentType'];
                        $user->city = $employee['city'];
                        $user->email = $employee['email'];
                        $user->department_id = $cost_center->id;
                        if($employee['oldDOJ'] != '0000-00-00'){
                            $user->old_date_of_joining = $employee['oldDOJ'];
                        }
                        else{
                            $user->old_date_of_joining = NULL;
                        }
                        if($lineManager){
                            $user->leader_id = $lineManager->id;
                            Log::info('Setting Line Manager: '.$lineManager->employee_number);
                        }
                        else{
                            $user->leader_id = NULL;
                        }
                        if($employee['location']){
                            $user->location = $employee['location'];
                        }
                        if($employee['Religion']){
                            $user->religion = $employee['Religion'];
                        }
                        $user->save();
                    }
                    catch(\Exception $e){
                        Log::info('------------- START EXCEPTION DURING EMPLOYEE INSERTION -------------');
                        Log::info('Error #: '.$e->getCode());
                        Log::info('Error Description: '.$e->getMessage());
                        Log::info('------------- END EXCEPTION DURING EMPLOYEE INSERTION -------------');
                    }
                }
                else{
                    Log::info('------------- START EXCEPTION DURING EMPLOYEE INSERTION -------------');
                    Log::info('Employee #: '.$employee['employeeNumber']);
                    Log::info('Hint: Cost Center or Grade does not exist');
                    Log::info('------------- END EXCEPTION DURING EMPLOYEE INSERTION -------------');

                }
            }
            else{
                $grade = Grade::with('company')
                    ->whereHas('company', function ($query) use ($employeeCompanyKey){
                        $query->where('companies.company_key', '=', $employeeCompanyKey);
                    })->where('secondary_name', '=', $employee['grade'])
                    ->first();
                $cost_center = Department::with('businessUnit.company')
                    ->whereHas('businessUnit.company', function($query) use ($employeeCompanyKey){
                        $query->where('companies.company_key', '=', $employeeCompanyKey);
                    })->where('cost_center', '=', $employee['costCenter'])
                    ->first();
                $lineManager = Employee::with('department.businessUnit.company')
                    ->whereHas('department.businessUnit.company', function($query) use ($lineManagerCompanyKey){
                        $query->where('companies.company_key', '=', $lineManagerCompanyKey);
                    })->where('user_name', '=', $employee['lineManagerID'])
                    ->first();
                    
                      log::info('line manager : '.$lineManager); 

                if(isset($grade) && isset($cost_center)){
                    try{
                        $user = new Employee();
                        $user->employee_number = $employee['employeeNumber'];
                        $user->employee_name = $employee['employeeName'];
                        $user->designation = $employee['designation'];
                        $user->grade_id = $grade->id;
                        $user->grade = $grade->primary_name;
                        $user->cnic = $employee['cnic'];
                        $user->date_of_joining = $employee['dateOfJoining'];
                        $user->date_of_birth = $employee['dateOfBirth'];
                        $user->address = $employee['address'];
                        $user->marital_status = $employee['maritalStatus'];
                        $user->qualification = $employee['qualification'];
                        $user->father_name = $employee['fatherName'];
                        $user->employment_type = $employee['employmentType'];
                        $user->city = $employee['city'];
                        $user->department_id = $cost_center->id;
                        $user->account_type = 2;
                        $user->level = 0;
                        $user->user_name = $employee['employeeNumber'];
                        $user->password = md5($employee['employeeNumber'].'123');
                        if($employee['oldDOJ'] != '0000-00-00'){
                            $user->old_date_of_joining = $employee['oldDOJ'];
                        }
                        else{
                            $user->old_date_of_joining = NULL;
                        }
                        if(isset($lineManager)){
                            $user->leader_id = $lineManager->id;
                        }
                        else{
                            $user->leader_id = NULL;
                        }
                        if($employee['location']){
                            $user->location = $employee['location'];
                        }
                        if($employee['Religion']){
                            $user->religion = $employee['Religion'];
                        }
                        $user->save();
                    }
                    catch(\Exception $e){
                        Log::info('------------- START EXCEPTION DURING EMPLOYEE INSERTION -------------');
                        Log::info('Error #: '.$e->getCode());
                        Log::info('Error Description: '.$e->getMessage());
                        Log::info('------------- END EXCEPTION DURING EMPLOYEE INSERTION -------------');
                    }
                }
                else{
                    Log::info('------------- START EXCEPTION DURING EMPLOYEE INSERTION -------------');
                    Log::info('Employee #: '.$employee['employeeNumber']);
                    Log::info('Hint: Cost Center or Grade does not exist');
                    Log::info('------------- END EXCEPTION DURING EMPLOYEE INSERTION -------------');
                }
            }
        }
        $employee_id = '6042';
        $employee = Employee::where('id',$employee_id)->first();
        if($employee->leader_id == NULL){
            $employee->leader_id = '3385';
            $employee->save();
        }
        /*-------------------------------------------------------------------
         * As Requested by Amjad Sheikh; Hard coding SAP sync for SBA, SHA & SHBA
         * -------------------------------------------------------------------
         * */
        $employee_id = '1715';
        $employee = Employee::where('id',$employee_id)->first();
        if($employee->email != 'naveed.ahmad@packages.com.pk') {
            $employee->email = 'naveed.ahmad@packages.com.pk';
            $employee->save();
        }
        $employee_id = '1706';
        $employee = Employee::where('id',$employee_id)->first();
        if($employee->email != 'jazib.faizi@packages.com.pk') {
            $employee->email = 'jazib.faizi@packages.com.pk';
            $employee->save();
        }
        $employee_id = '1705';
        $employee = Employee::where('id',$employee_id)->first();
        if($employee->email != 'naveed.ahmad@packages.com.pk') {
            $employee->email = 'naveed.ahmad@packages.com.pk';
            $employee->save();
        }
        return 'Success';
    }

    public function sapUpdateLeavers(){
        $entityBody = file_get_contents('php://input');
        Log::info('---------------SAP Pushed Leavers-------------------');
        Log::info($entityBody);
        Log::info('---------------SAP Pushed Leavers-------------------');

        $data = explode("\n", $entityBody);
        $employeeData = array();
        $employees = array();
        foreach($data as $row){
            $columns = explode('^', $row);
            Log::info('-------------Columns----------------------');
            Log::info($columns);
            Log::info('-----------------------------------');
            foreach($columns as $column) {
                Log::info('-------------Column----------------------');
                Log::info($column);
                Log::info('-----------------------------------');
                $key = explode("=", $column)[0];
                if(isset($column[1])){
                $value = explode("=", $column)[1];
                array_set($employeeData, $key,$value);
                }
            }
            array_push($employees, $employeeData);
        }
        foreach($employees as $employee){
            $employee_id = $employee['employeeNumber'];
            $employeeCompanyKey = $employee['employeeCompanyKey'];
            $leaveDate = $employee['leaveDate'];
            Log::info('-----------------------------------');
            Log::info('Processing Employee #: '.$employee['employeeNumber']);
            Log::info('Processing Employee Company Key: '.$employee['employeeCompanyKey']);
            Log::info('Processing Employee Leave Date: '.$employee['leaveDate']);
            Log::info('-----------------------------------');

            $user = Employee::with('department.businessUnit.company')
                ->whereHas('department.businessUnit.company', function($query) use ($employeeCompanyKey){
                    $query->where('company_key', '=', $employeeCompanyKey);
                })->where('user_name', '=', $employee_id)
                ->first();
            if($user && $leaveDate){
                $user->last_date_of_working = $employee['leaveDate'];
                $user->save();
            }
        }
        return 'Success';
    }

	public function sapUpdateOld(Request $request){
		$entityBody = file_get_contents('php://input');
		Log::info('---------------SAP Pushed-------------------');
		Log::info($entityBody);
		Log::info('---------------SAP Pushed-------------------');

		$data = explode("\n", $entityBody);
		$employeeData = array();
		$employees = array();
		foreach($data as $row){
			$columns = explode('^', $row);
			foreach($columns as $column) {
				$key = explode("=", $column)[0];
				$value = explode("=", $column)[1];
				array_set($employeeData, $key,$value);
			}
			array_push($employees, $employeeData);
		}
		foreach($employees as $employee){
			$employee_id = $employee['employeeNumber'];
			Log::info('-----------------------------------');
			Log::info('Processing Employee #: '.$employee['employeeNumber']);
			Log::info('-----------------------------------');
			$user = Employee::where('user_name', '=', $employee_id)->first();
			if(isset($user)){
				$grade = Grade::where('secondary_name', '=', $employee['grade'])->first();
                $cost_center = Department::where('cost_center', '=', $employee['costCenter'])->first();
                $lineManager = Employee::where('user_name', '=', $employee['lineManagerID'])->first();
                Log::info('Line Manager details' . $lineManager);
                if(isset($grade) && isset($cost_center)){
                    try{
                        $user->employee_name = $employee['employeeName'];
                        $user->designation = $employee['designation'];
                        $user->grade = $grade->id;
                        $user->cnic = $employee['cnic'];
                        $user->date_of_joining = $employee['dateOfJoining'];
                        $user->date_of_birth = $employee['dateOfBirth'];
                        $user->address = $employee['address'];
                        $user->marital_status = $employee['maritalStatus'];
                        $user->qualification = $employee['qualification'];
                        $user->father_name = $employee['fatherName'];
                        $user->employment_type = $employee['employmentType'];
                        $user->city = $employee['city'];
                        $user->department_id = $cost_center->id;
                        if(isset($lineManager)){
                            $user->leader_id = $lineManager->id;
                        }
                        else{
                            $user->leader_id = NULL;
                        }
                        $user->save();
                    }
                    catch(\Exception $e){
                        Log::info('------------- START EXCEPTION DURING EMPLOYEE INSERTION -------------');
                        Log::info('Error #: '.$e->getCode());
                        Log::info('Error Description: '.$e->getMessage());
                        Log::info('------------- END EXCEPTION DURING EMPLOYEE INSERTION -------------');
                    }
                }
                else{
                    Log::info('------------- START EXCEPTION DURING EMPLOYEE INSERTION -------------');
                    Log::info('Employee #: '.$employee['employeeNumber']);
                    Log::info('Hint: Cost Center or Grade does not exist');
                    Log::info('------------- END EXCEPTION DURING EMPLOYEE INSERTION -------------');

                }
			}
			else{
				$grade = Grade::where('secondary_name', '=', $employee['grade'])->first();
				$cost_center = Department::where('cost_center', '=', $employee['costCenter'])->first();
                $lineManager = Employee::where('user_name', '=', $employee['lineManagerID'])->first();
                Log::info('Line Manager' . $lineManager);
                if(isset($grade) && isset($cost_center)){
                    try{
                        $user = new Employee();
                        $user->employee_number = $employee['employeeNumber'];
                        $user->employee_name = $employee['employeeName'];
                        $user->designation = $employee['designation'];
                        $user->grade = $grade->id;
                        $user->cnic = $employee['cnic'];
                        $user->date_of_joining = $employee['dateOfJoining'];
                        $user->date_of_birth = $employee['dateOfBirth'];
                        $user->address = $employee['address'];
                        $user->marital_status = $employee['maritalStatus'];
                        $user->qualification = $employee['qualification'];
                        $user->father_name = $employee['fatherName'];
                        $user->employment_type = $employee['employmentType'];
                        $user->city = $employee['city'];
                        $user->department_id = $cost_center->id;
                        $user->account_type = 2;
                        $user->level = 0;
                        $user->user_name = $employee['employeeNumber'];
                        $user->password = md5($employee['employeeNumber'].'123');
                        if(isset($lineManager)){
                            $user->leader_id = $lineManager->id;
                        }
                        else{
                            $user->leader_id = NULL;
                        }
                        $user->save();
                    }
                    catch(\Exception $e){
                        Log::info('------------- START EXCEPTION DURING EMPLOYEE INSERTION -------------');
                        Log::info('Error #: '.$e->getCode());
                        Log::info('Error Description: '.$e->getMessage());
                        Log::info('------------- END EXCEPTION DURING EMPLOYEE INSERTION -------------');
                    }
                }
                else{
                    Log::info('------------- START EXCEPTION DURING EMPLOYEE INSERTION -------------');
                    Log::info('Employee #: '.$employee['employeeNumber']);
                    Log::info('Hint: Cost Center or Grade does not exist');
                    Log::info('------------- END EXCEPTION DURING EMPLOYEE INSERTION -------------');
                }
			}
		}
		return 'Success';
	}

	public function sapUpdateEmployeeFamily(){
		$entityBody = file_get_contents('php://input');
		$data = explode("\n", $entityBody);
		$employeeFamilyData = array();
		$employeeFamily = array();
		foreach($data as $row){
			$columns = explode('^', $row);
			foreach($columns as $column) {
			    Log::info('-----------------------------------');
	        	Log::info('column :'.$column);
					if(isset(explode("=", $column)[0])){
				    	$key = explode("=", $column)[0];
	        	}
			
				Log::info('-----------------------------------');
	        	Log::info('key :'.$key);
	        	if(isset(explode("=", $column)[1])){
				    $value = explode("=", $column)[1];
	        	}
				Log::info('-----------------------------------');
	        	Log::info('value :'.$value);
				array_set($employeeFamilyData, $key,$value);
			}
			array_push($employeeFamily, $employeeFamilyData);
		}

		$families = Employee_Dependent::whereNull('is_local')->get();
		foreach($families as $family){
			$family->delete();
		}

		foreach($employeeFamily as $family){
			Log::info('-----------------------------------');
			Log::info('Processing Family of Employee #: '.$family['employeeNumber']);
			$data = new Employee_Dependent();
			$data->dependent_name = $family['fName'];
			$data->relation = $family['relation'];
			$data->date_of_birth = $family['dateOfBirth'];
			$data->employee_id = $family['employeeNumber'];
			$data->save();
			Log::info('Successfuly Process');
			Log::info('-----------------------------------');
		}
	}
}
