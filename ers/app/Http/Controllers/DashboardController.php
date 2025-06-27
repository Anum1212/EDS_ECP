<?php

namespace App\Http\Controllers;

use App\Category;
use App\Department;
use App\Employee;
use App\Http\Controllers\Controller;

use App\Rate;
use App\Voucher;
use App\Voucher_Category;
use App\Voucher_Employee;
use App\Voucher_Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
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
            $data['customView'] = 'custom-' . $custom_view . '.dashboard';
            return view('basic.dashboard', $data);
        } else {
            Session::flash('error', 'Your session has ended. Please login to continue.');
            return redirect('/');
        }
    }

    public function maintenance()
    {
        return view('basic.maintenance');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
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

    public function autoReject()
    {
        $vouchers = Voucher::with('voucherStatus')->where('status', '=', 'Submitted')->get();
        $data['employee'] = Employee::where('user_name', '=', 'ers-admin')->first();
        foreach ($vouchers as $voucher) {
            $difference = date_diff(date_create(date('Y-m-d')), date_create($voucher->voucherStatus[count($voucher->voucherStatus) - 1]->pivot->created_at))->format('%a');
            if ($difference > 7 && $voucher->voucherStatus[count($voucher->voucherStatus) - 1]->pivot->approved == NULL) {
                DB::beginTransaction();

                $data['voucher'] = $voucher;
                $data['client'] = Employee::find($voucher->employee->id);
                $data['rejectionComments'] = 'Auto rejection - Voucher not approved within 14 days';
                /*$mail = new MailController();
                $mail->send($data,$data,'voucher-rejected', 'Voucher #'.$voucher->id.' Rejected');*/
            }

            if ($difference > 14 && $voucher->voucherStatus[count($voucher->voucherStatus) - 1]->pivot->approved == NULL) {
                DB::beginTransaction();

                $voucher->status = 'Rejected';
                //$voucher->save();

                $voucherStatus = new Voucher_Employee();
                $voucherStatus->voucher_id = $voucher->id;
                $voucherStatus->employee_id = $data['employee']->id;
                $voucherStatus->approved = 0;
                $voucherStatus->comments = 'Auto rejection - Voucher not approved within 14 days';
                //$voucherStatus->save();
                //DB::commit();

                $data['voucher'] = $voucher;
                $data['client'] = Employee::find($voucher->employee->id);
                $data['rejectionComments'] = 'Auto rejection - Voucher not approved within 14 days';
                /*$mail = new MailController();
                $mail->send($data,$data,'voucher-rejected', 'Voucher #'.$voucher->id.' Rejected');*/
            }
        }
    }

    public function checkAPI()
    {
        $employee_id = Session::get('id');
        if (isset($employee_id)) {
            $data['path'] = Route::getFacadeRoot()->current()->uri();
            $data['employee'] = Employee::find($employee_id);
            $custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
            $data['customView'] = 'custom-' . $custom_view . '.dashboard';
            $data['customLayout'] = 'custom-' . $data['employee']->custom_view . '.layout.ers-layout';

            $data['columns'] = Schema::getColumnListing('voucher_items');
            $data['departments'] = Department::all();
            $data['categories'] = Category::all();
            $data['employees'] = Employee::all();
            $data['statuses'] = Voucher::select('status')->distinct('status')->get();
            return view('basic.stats-generic', $data);
        } else {
            Session::flash('error', 'Your session has ended. Please login to continue.');
            return redirect('/');
        }
    }

    public function storeCheckApi(Request $request)
    {
        $employee_id = Session::get('id');
        if (isset($employee_id)) {
            $data['path'] = Route::getFacadeRoot()->current()->uri();
            $data['employee'] = Employee::find($employee_id);
            $custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
            $data['customView'] = 'custom-' . $custom_view . '.stats';
            $data['customLayout'] = 'custom-' . $data['employee']->custom_view . '.layout.ers-layout';

            $categories = $request->input('categories');
            $cost_centers = $request->input('cost_centers');
            $statuses = $request->input('statuses');
            $employees = $request->input('employees');
            $columns = $request->input('columns');
            $from = $request->input('from');
            $to = $request->input('to');

            if (count($columns) == 0) {
                $columns = Schema::getColumnListing('voucher_items');
            }
            array_push($columns, 'voucher_id');
            array_push($columns, 'category_id');

            $voucherItems = Voucher_Item::with('voucher', 'voucher.employee.department', 'voucher.employee', 'category', 'voucher.voucherStatus')
                ->whereHas('category', function ($query) use ($categories) {
                    if (count($categories) > 0) {
                        $query->whereIn('categories.id', $categories);
                    }
                })->whereHas('voucher', function ($query) use ($statuses, $cost_centers, $from, $to) {
                    if (count($statuses) > 0) {
                        $query->whereIn('status', $statuses);
                    }
                    if (count($cost_centers) > 0) {
                        $query->whereIn('charge_to_cost_center', $cost_centers);
                    }
                    $query->where('processed_at', '>=', $from)
                        ->where('processed_at', '<=', $to);
                })->whereHas('voucher.employee', function ($query) use ($employees) {
                    if (count($employees) > 0) {
                        $query->whereIn('id', $employees);
                    }
                })->select($columns)->get();

            $data['columns'] = $columns;
            $data['voucherItems'] = $voucherItems;
            return view('basic.stats-report-generic', $data);
        } else {
            Session::flash('error', 'Your session has ended. Please login to continue.');
            return redirect('/');
        }
    }

    public function updateData()
    {
        $count = 0;
        $newCompany = 13;
        /**
         * UPDATING DEPARTMENTS IN VOUCHERS CHARGE TO COST CENTER
         *
         */
        /*$vouchers = Voucher::all();
	    foreach ($vouchers as $voucher){
	        if($voucher->charge_to_cost_center && $voucher->charge_to_cost_center != ''){
                $department = Department::with('businessUnit.company')->whereHas('businessUnit.company', function($query){
                    $query->where('companies.id', '=', 1);
                })->where('cost_center', '=', $voucher->charge_to_cost_center)
                    ->first();
            }
	        else{
                $department = Department::with('businessUnit.company')->whereHas('businessUnit.company', function($query) use ($voucher){
                    $query->where('companies.id', '=', $voucher->employee->department->businessUnit->company->id);
                })->where('cost_center', '=', $voucher->employee->department->cost_center)
                    ->first();
            }
	        if($department){
	            $voucher->department_id = $department->id;
	            $voucher->save();
	            $count++;
            }
        }
	    echo 'Total Vouchers: '.count($vouchers);
	    echo '<br>';
	    echo 'Total Processed: '.$count;*/

        /**
         * UPDATING GRADES IN EMPLOYEES WITH ID
         *
         */
        /*$employees = Employee::all();
        foreach ($employees as $employee){
            $company_id  = $employee->department->businessUnit->company->id;
            $company = Company::find($company_id);
            if($company){
                $grade = Grade::with('company')
                    ->whereHas('company', function($query) use ($company){
                        $query->where('companies.id', '=', $company->id);
                    })->where('primary_name', '=', $employee->grade)
                    ->first();
                if($grade){
                    $employee->grade_id = $grade->id;
                    $employee->save();
                    $count++;
                }
            }
        }
        echo 'Total Employees: '.count($employees);
        echo '<br>';
        echo 'Total Processed: '.$count;*/

        /**
         * UPDATING RATES WITH GRADE NULL
         *
         */
        /*$rates = Rate::all();
        $grades = Grade::with('company')->whereHas('company', function($query){
            $query->where('companies.id','=',1);
        })->get();
        foreach ($rates as $rate) {
            if(!$rate->grade){
                foreach ($grades as $grade){
                    $newRate = new Rate();
                    $newRate->rate_from = $rate->rate_from;
                    $newRate->rate_to = $rate->rate_to;
                    $newRate->rate_name = $rate->rate_name;
                    $newRate->currency = $rate->currency;
                    $newRate->amount = $rate->amount;
                    $newRate->grade = $grade->primary_name;
                    $newRate->save();
                }
            }
        }*/
        /**
         * UPDATING GRADES OF RATE
         *
         */
        /*$rates = Rate::all();
        foreach ($rates as $rate){
            $grade = Grade::with('company')->whereHas('company', function($query){
                $query->where('companies.id','=',1);
            })->where('primary_name', '=', $rate->grade)->first();
            if($grade){
                $rate->grade_id = $grade->id;
                $rate->save();
            }
        }*/
        /**
         * MAKING GRADES COPY FOR
         * PACKAGES CONVERTERS
         *
         */
        /*$grades = Grade::with('company')->whereHas('company', function($query){
            $query->where('companies.id','=',1);
        })->get();

        foreach ($grades as $grade){
            $newGrade = Grade::where('secondary_name', '=', $grade->secondary_name)->where('company_id', '=', $newCompany)->first();
            if(!$newGrade){
                $newGrade = new Grade();
            }
            $newGrade->primary_name = $grade->primary_name;
            $newGrade->secondary_name = $grade->secondary_name;
            $newGrade->overtime_applicable = $grade->overtime_applicable;
            $newGrade->overtime_margin = $grade->overtime_margin;
            $newGrade->compensatory_leave_applicable = $grade->compensatory_leave_applicable;
            $newGrade->late_margin = $grade->late_margin;
            $newGrade->night_shift_allowance = $grade->night_shift_allowance;
            $newGrade->break_entry = $grade->break_entry;
            $newGrade->leave_group_id = $grade->leave_group_id;
            $newGrade->company_id = $newCompany;
            $newGrade->save();
        }*/
        /**
         * MAKING CATEGORIES COPY FOR
         * PACKAGES CONVERTERS
         *
         */
        /*$categories = Category::with('company')->whereHas('company', function($query){
            $query->where('companies.id','=',1);
        })->get();
        foreach ($categories as $category) {
            $newCategory = Category::where('category_name', '=', $category->category_name)->where('company_id', '=', $newCompany)->first();
            if(!$newCategory){
                $newCategory = new Category();
            }
            $newCategory->category_name = $category->category_name;
            $newCategory->approval_steps = $category->approval_steps;
            $newCategory->protocol = $category->protocol;
            $newCategory->description = $category->description;
            $newCategory->view = $category->view;
            $newCategory->enabled = $category->enabled;
            $newCategory->duplication_check = $category->duplication_check;
            $newCategory->voucher_suitable = $category->voucher_suitable;
            $newCategory->travel_order_suitable = $category->travel_order_suitable;
            $newCategory->company_id = $newCompany;
            $newCategory->save();
        }*/
        /**
         * MAKING BUSINESS UNITS AND DEPARTMENTS COPY FOR
         * PACKAGES CONVERTERS
         *
         */
        /*$businessUnits = Business_Unit::with('company')->whereHas('company', function($query){
            $query->where('companies.id','=',1);
        })->get();
        foreach ($businessUnits as $business_Unit){
            $newBusinessUnit = Business_Unit::where('company_id', '=', $newCompany)->where('bu_name', '=', $business_Unit->bu_name)->first();
            if(!$newBusinessUnit){
                $newBusinessUnit = new Business_Unit();
            }
            $newBusinessUnit->bu_name = $business_Unit->bu_name;
            $newBusinessUnit->company_id = $newCompany;
            $newBusinessUnit->save();
            foreach ($business_Unit->departments as $department){
                $newDepartment = Department::with('businessUnit.company')->where('department_name', '=', $department->department_name)
                    ->where('classification', '=', $department->classification)
                    ->where('cost_center', '=', $department->cost_center)
                    ->whereHas('businessUnit.company', function($query) use ($newCompany){
                        $query->where('companies.id', '=', $newCompany);
                    })->first();
                if(!$newDepartment){
                    $newDepartment = new Department();
                }
                $newDepartment->department_name = $department->department_name;
                $newDepartment->cost_center = $department->cost_center;
                $newDepartment->classification = $department->classification;
                $newDepartment->business_unit_id = $newBusinessUnit->id;
                $newDepartment->save();
            }
        }*/

        /**
         * MAKING GL MAPPINGS COPY FOR
         * PACKAGES CONVERTERS
         *
         */
        /*$vouchersCategories = Category::with('company')->whereHas('company', function($query){
            $query->where('companies.id', '=', 13);
        })->get();
        foreach ($vouchersCategories as $vouchersCategory) {
            $packagesVoucherCategory = Category::where('category_name', '=', $vouchersCategory->category_name)->where('company_id', '=', 1)->first();
            foreach ($packagesVoucherCategory->glMappings as $glMapping){
                $newGLMapping = GL_Mapping::where('category_id', '=', $vouchersCategory->id)
                    ->where('classification', '=', $glMapping->classification)
                    ->where('gl_account', '=', $glMapping->gl_account)
                    ->first();
                if(!$newGLMapping){
                    $newGLMapping = new GL_Mapping();
                }
                $newGLMapping->category_id = $vouchersCategory->id;
                $newGLMapping->classification = $glMapping->classification;
                $newGLMapping->gl_account = $glMapping->gl_account;
                $newGLMapping->save();
            }
        }*/

        /**
         * MAKING RATES COPY FOR
         * PACKAGES CONVERTERS
         *
         */
        /*$rates = Rate::with('rateGrade.company')->whereHas('rateGrade.company', function($query){
            $query->where('companies.id', '=', 1);
        })->whereNotNull('grade')->get();
        foreach ($rates as $rate){
            $newRate = Rate::with('rateGrade.company')->whereHas('rateGrade.company', function($query) use ($newCompany){
                $query->where('companies.id', '=', $newCompany);
            })->where('rate_name', '=', $rate->rate_name)
                ->where('grade', '=', $rate->grade)
                ->where('rate_from', '=', $rate->rate_from)
                ->where('rate_to', '=', $rate->rate_to)
                ->first();
            if(!$newRate){
                $newRate = new Rate();
            }
            $rateGrade = Grade::where('company_id', '=', $newCompany)->where('primary_name', '=', $rate->grade)->first();
            if($rateGrade){
                $newRate->rate_from = $rate->rate_from;
                $newRate->rate_to = $rate->rate_to;
                $newRate->rate_name = $rate->rate_name;
                $newRate->currency = $rate->currency;
                $newRate->amount = $rate->amount;
                $newRate->grade  = $rate->grade;
                $newRate->grade_id  = $rateGrade->id;
                $newRate->save();
            }
        }*/

        /**
         * MAKING APPROVAL MATRIX COPY FOR
         * PACKAGES CONVERTERS
         *
         */
        /*$approvalMetrics = Business_Unit::has('approvers')->with('approvers')->get();
        foreach ($approvalMetrics as $approvalMetric){
            $businessUnit = Business_Unit::where('company_id', '=', $newCompany)->where('bu_name', '=', $approvalMetric->bu_name)->first();
            if($businessUnit){
                foreach ($approvalMetric->approvers as $approver){
                    $businessUnitEmployee = new BusinessUnitEmployee();
                    $businessUnitEmployee->employee_id = $approver->id;
                    $businessUnitEmployee->business_unit_id = $businessUnit->id;
                    $businessUnitEmployee->save();
                }
            }
        }*/
    }
}
