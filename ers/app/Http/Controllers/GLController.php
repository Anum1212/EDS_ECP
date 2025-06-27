<?php namespace App\Http\Controllers;

use App\Category;
use App\Department;
use App\Employee;
use App\GL_Mapping;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class GLController extends Controller {

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

			if($data['employee']->account_type == 1 || $data['employee']->account_type == 0){
                // $data['glMappings'] = GL_Mapping::all();
                $data['glMappings'] = GL_Mapping::join('categories','gl_mappings.category_id','=','categories.id')
                ->join('companies','categories.company_id','=','companies.id')->select('gl_mappings.*','categories.category_name','companies.company_name')->get();
            }
			else{

				// $data['glMappings'] = GL_Mapping::all();
				$data['glMappings'] = GL_Mapping::join('categories','gl_mappings.category_id','=','categories.id')->select('gl_mappings.*','categories.category_name','companies.company_name')->get();
			}
            // dd($data['glMappings'][0]);
			return view('basic.gl-mappings', $data);
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
            $data['customView'] = 'custom-'.$custom_view.'.add-department';
			$data['classifications'] = Department::select('classification')->where('classification', '!=', "")->distinct('classification')->orderby('classification', 'ASC')->get();
			$data['categories'] = Category::all();
			return view('basic.add-gl-account', $data);
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
			$glAccount = array(
				"category" => "Category",
				"classification" => "Classification",
				"gl_account" => "GL Account"
			);
			$validator = Validator::make($request->all(),
				[
					"category" => "required",
					"classification" => "required",
					"gl_account" => "required"
				]);
			$validator->setAttributeNames($glAccount);
			if ($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			}
			else {
				DB::beginTransaction();
				$glAccount = GL_Mapping::where('classification', '=', $request->input('classification'))->where('category_id', '=', $request->input('category'))->first();
				if(isset($glAccount)){
					goto makeGLAccount;
				}
				else{
					$glAccount = new GL_Mapping();
					makeGLAccount:
					$glAccount->category_id = $request->input('category');
					$glAccount->classification = $request->input('classification');
					$glAccount->gl_account = $request->input('gl_account');
					$glAccount->save();
				}

				DB::commit();

				Session::flash('success', 'GL Account has been added successfuly');
				return redirect('gl-accounts');
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

	public function getGLMapping(Request $request){
		$category = $request->input('category');
		$classification = $request->input('classification');
		$glMapping = GL_Mapping::where('category_id','=',$category)->where('classification', '=', $classification)->first();
		if(isset($glMapping)){
			return response(json_encode($glMapping));
		}
		else{
			return json_encode('Not Found');
		}
	}

}
