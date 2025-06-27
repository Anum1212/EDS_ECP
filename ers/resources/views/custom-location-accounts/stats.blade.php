{{--*/ $employees = \App\Employee::with('department', 'department.businessUnit', 'divisionsApprover')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})->get(); /*--}}
{{--*/ $statuses = \App\Voucher::select('status')->distinct('status')->get(); /*--}}
{{--*/ $categories = \App\Category::whereHas('company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})->get(); /*--}}
{{--*/ $departments = \App\Department::whereHas('businessUnit.company',function($query) use ($employee){ $query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name); })->get() /*--}}
<div class="content-body">
    <section id="form-control-repeater">
        <form action="{{URL::to('extract/stats')}}" method="post">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-gradient-x-grey-blue">
                            <h4 class="card-title white" id="file-repeater">Basic Information for <h4 class="white">Stats Generation</h4></h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-h font-medium-3"></i></a>
                        </div>
                        <div class="card-content collapse show">
                            <div class="card-body">
                                <div class="form row">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="form-group col-md-2 mb-2">
                                        <label>From</label>
                                        <input type="date" class="form-control" value="{{date('Y-m-d', strtotime(date('Y-m-d').'- 1 month'))}}" name="from" required>
                                    </div>
                                    <div class="form-group col-md-2 mb-2">
                                        <label>To</label>
                                        <input type="date" class="form-control" value="{{date('Y-m-d')}}" name="to" required>
                                    </div>
                                </div>
                                <div class="form row">
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Employees</label>
                                        <select class="select2 form-control costCenters" name="employees[]" multiple>
                                            @foreach($employees as $item)
                                                <option value="{{$item->id}}">{{$item->employee_number.' / '.$item->employee_name.' - '.$item->department->businessUnit->company->company_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Charged To Cost Centers</label>
                                        <select class="select2 form-control costCenters" name="cost_centers[]" multiple>
                                            @foreach($departments as $department)
                                                <option value="{{$department->id}}">{{$department->cost_center.' / '.$department->department_name.' - '.$department->businessUnit->company->company_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Categories</label>
                                        <select class="select2 form-control categories" name="categories[]" multiple>
                                            @foreach($categories as $category)
                                                <option value="{{$category->id}}">{{$category->category_name.' - '.$category->company->company_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form row">
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Select Status</label>
                                        <select class="select2 form-control statuses" name="statuses[]" multiple>
                                            @foreach($statuses as $status)
                                                <option value="{{$status->status}}">{{$status->status}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Select Columns</label>
                                        <select class="select2 form-control statuses" name="columns[]" multiple>
                                            @foreach($columns as $column)
                                                <option value="{{$column}}">{{ucwords(str_replace('_', ' ',$column))}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-info btn-sm" type="submit"><i class="la la-paper-plane-o"></i> Submit </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>