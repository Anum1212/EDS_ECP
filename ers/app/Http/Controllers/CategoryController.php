<?php namespace App\Http\Controllers;

use App\Category;
use App\Employee;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller {

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
            $data['customView'] = 'custom-'.$custom_view.'.categories';

			return view('basic.categories', $data);
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
            $data['customView'] = 'custom-'.$custom_view.'.add-category';
			$data['categories'] = Category::all();
			return view('basic.add-category', $data);
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
			$category = array(
				"category_name" => "Category Name",
				"approval_steps" => "Approval Steps",
				"protocol" => "Protocols",
				"description" => "Description",
				"enabled" => "Category Enabled",
				"view" => "View",
				"duplication_check" => "Duplication Check",
				"company" => "Company"
			);
			$validator = Validator::make($request->all(),
				[
					"category_name" => "required|unique:categories,category_name",
					"view" => "required|unique:categories,view",
					"approval_steps" => "required",
					"protocol" => "required",
					"description" => "required",
					"enabled" => "required",
					"duplication_check" => "required",
					"company" => "required"
				]);
			$validator->setAttributeNames($category);
			if ($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			}
			else {
				DB::beginTransaction();
				$category = new Category();
				$category->category_name = $request->input('category_name');
				$category->approval_steps = $request->input('approval_steps');
				$category->protocol = $request->input('protocol');
				$category->description = $request->input('description');
				if($request->input('enabled') == 'yes'){
					$category->enabled = $request->input('enabled');
				}
				if($request->input('duplication_check') == 'yes'){
					$category->duplication_check = $request->input('duplication_check');
				}
				$category->view = $request->input('view');
				$category->company_id = $request->input('company');
				$category->save();
				DB::commit();

				Session::flash('success', 'Category has been added successfuly');
				return redirect('categories');
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
            $data['customView'] = 'custom-'.$custom_view.'.update-category';
            $data['category'] = Category::find($id);
			return view('basic.update-category', $data);
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
