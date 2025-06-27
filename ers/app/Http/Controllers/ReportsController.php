<?php namespace App\Http\Controllers;

use App\Category;
use App\Department;
use App\Employee;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Voucher;
use App\Voucher_Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class ReportsController extends Controller {

	public function stats(){
	    $employee_id = Session::get('id');
        if(isset($employee_id)){
            $data['path'] = Route::getFacadeRoot()->current()->uri();
            $data['employee'] = Employee::find($employee_id);
            $custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
            $data['customView'] = 'custom-'.$custom_view.'.stats';

            $data['columns'] = Schema::getColumnListing('voucher_items');
            return view('basic.stats', $data);
        }
        else{
            Session::flash('error', 'Your session has ended. Please login to continue.');
            return redirect('/');
        }
	}

	public function extractStats(Request $request){
        $employee_id = Session::get('id');
        if(isset($employee_id)){
            $data['path'] = Route::getFacadeRoot()->current()->uri();
            $data['employee'] = Employee::find($employee_id);
            $custom_view = \Illuminate\Support\Facades\Request::get('custom_view');
            $data['customView'] = 'custom-'.$custom_view.'.stats';

            $categories = $request->input('categories');
            $departments = $request->input('cost_centers');
            $statuses = $request->input('statuses');
            $employees = $request->input('employees');
            $columns = $request->input('columns');
            $from = $request->input('from');
            $to = $request->input('to');

            if(count($columns) == 0){
                $columns = Schema::getColumnListing('voucher_items');
            }
            array_push($columns, 'voucher_id');
            array_push($columns, 'category_id');

            $voucherItems = Voucher_Item::with('voucher', 'voucher.employee.department', 'voucher.employee', 'category', 'voucher.voucherStatus')
                ->whereHas('category',function($query) use ($categories){
                    if(count($categories) > 0){
                        $query->whereIn('categories.id', $categories);
                    }
                })->whereHas('voucher',function($query) use ($statuses, $from, $to){
                    if(count($statuses) > 0){
                        $query->whereIn('status',$statuses);
                    }
                    $query->where('processed_at', '>=', $from)
                        ->where('processed_at','<=', $to);
                })->whereHas('voucher.department', function($query) use ($departments){
                    if(count($departments) > 0){
                        $query->whereIn('id', $departments);
                    }
                })->whereHas('voucher.employee', function ($query) use ($employees){
                    if(count($employees) > 0){
                        $query->whereIn('id', $employees);
                    }
                })->select($columns)->get();
            $data['columns'] = $columns;
            $data['voucherItems'] = $voucherItems;
            return view('basic.stats-report', $data);
        }
        else{
            Session::flash('error', 'Your session has ended. Please login to continue.');
            return redirect('/');
        }
	}
}
