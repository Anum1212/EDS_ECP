{{--*/ $businessUnits = \App\Business_Unit::all(); /*--}}
{{--*/ $departments = \App\Department::all(); /*--}}
<div class="content-body">
    <section id="form-control-repeater">
        <form action="{{URL::to('employee/update'.'/'.$employeeToUpdate->id)}}" method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="file-repeater">Personal Information</h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-h font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-content collapse show">
                            <div class="card-body">
                                <div class="form row" id="bu-form">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="form-group col-md-4 mb-2">
                                        <label>ID</label>
                                        <input type="text" class="form-control" value="{{$employeeToUpdate->employee_number}}" name="id" required>
                                    </div>
                                    <div class="form-group col-md-8 mb-2">
                                        <label>Name</label>
                                        <input type="text" class="form-control" value="{{$employeeToUpdate->employee_name}}" name="name" required>
                                    </div>
                                    <div class="form-group col-md-12 mb-2">
                                        <label>Email</label>
                                        <input type="email" class="form-control" value="{{$employeeToUpdate->email}}" name="email" required>
                                    </div>
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Nick Name</label>
                                        <input type="text" class="form-control" value="{{$employeeToUpdate->nick_name}}" name="nick_name">
                                    </div>
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Mobile</label>
                                        <input type="text" class="form-control" value="{{$employeeToUpdate->mobile}}" name="mobile" required>
                                    </div>
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Date of Birth</label>
                                        <input type="date" class="form-control" value="{{$employeeToUpdate->date_of_birth}}" name="date_of_birth" required>
                                    </div>
                                    <div class="form-group col-md-8 mb-2">
                                        <label>Address</label>
                                        <input type="text" class="form-control" value="{{$employeeToUpdate->address}}" name="address">
                                    </div>
                                    <div class="form-group col-md-4 mb-2">
                                        <label>City</label>
                                        <input type="text" class="form-control" value="{{$employeeToUpdate->city}}" name="city">
                                    </div>
                                    <div class="form-group col-md-4 mb-2">
                                        <label>CNIC</label>
                                        <input type="text" class="form-control" value="{{$employeeToUpdate->cnic}}" name="cnic" required>
                                    </div>
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Marital Status</label>
                                        <select class="select2 form-control marital_status" name="marital_status" required>
                                            <option value="Single" {{$employeeToUpdate->marital_status == 'Single'?"selected":""}}>Single</option>
                                            <option value="Married" {{$employeeToUpdate->marital_status == 'Married'?"selected":""}}>Married</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="file-repeater">Vehicle Information</h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-h font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-content collapse show">
                            <div class="card-body">
                                <div class="form row" id="vehicleContainer">
                                    @if(count($employeeToUpdate->vehicles))
                                        @foreach($employeeToUpdate->vehicles as $vehicle)
                                            <div class="form-row col-md-12">
                                                <div class="form-group col-md-4 mb-2">
                                                    <label>Vehicle #</label>
                                                    <input type="text" class="form-control" value="{{$vehicle->vehicle_number}}" name="vehicle_number[]" required>
                                                </div>
                                                <div class="form-group col-md-6 mb-2">
                                                    <label>Vehicle Description</label>
                                                    <input type="text" class="form-control" value="{{$vehicle->vehicle_description}}" name="vehicle_description[]" required>
                                                </div>
                                                <div class="form-group col-md-2" style="margin-top: 2.0rem">
                                                    <a class="btn btn-danger btn-sm white" id="removeVehicle"><i class="la la-trash-o"></i></a>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer">
                                <a class="btn btn-info btn-sm white" id="addVehicle"><i class="la la-plus-circle"></i> Add More Vehicle </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="file-repeater">Employment Information</h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-h font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-content collapse show">
                            <div class="card-body">
                                <div class="form row" id="bu-form">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="form-group col-md-12 mb-2">
                                        <label>Select Department <br><small>(Company Name - Business Unit - Department) / Cost Center</small></label>
                                        <select class="select2 form-control" name="department" required>
                                            @foreach($departments as $department)
                                                <option {{$employeeToUpdate->department_id == $department->id ? 'selected="Selected"':""}} value="{{$department->id}}">{{$department->businessUnit->company->company_name.' - '.$department->businessUnit->bu_name.' - '.$department->department_name .' / '.$department->cost_center}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Designation</label>
                                        <input type="text" class="form-control" value="{{$employeeToUpdate->designation}}" name="designation" required>
                                    </div>
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Grade</label>
                                        <input type="text" class="form-control" value="{{$employeeToUpdate->grade}}" name="grade" required>
                                    </div>
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Date of Joining</label>
                                        <input type="date" class="form-control" value="{{$employeeToUpdate->date_of_joining}}" name="date_of_joining" required>
                                    </div>
                                    <div class="form-group col-md-4 mb-2">
                                        <label>User Name</label>
                                        <input type="text" class="form-control" value="{{$employeeToUpdate->user_name}}" name="user_name" required>
                                    </div>
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Level</label>
                                        <input type="text" class="form-control" value="{{$employeeToUpdate->level}}" name="level" required>
                                    </div>
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Is Director</label>
                                        <select class="select2 form-control is_director" name="is_director" required>
                                            <option value="No" {{$employeeToUpdate->is_director == null?"selected":""}}>No</option>
                                            <option value="Yes" {{$employeeToUpdate->is_director == '1'?"selected":""}}>Yes</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Custom View</label>
                                        <input type="text" class="form-control" value="{{$employeeToUpdate->custom_view}}" name="custom_view">
                                    </div>
                                    <div class="form-group col-md-12 mb-2">
                                        <label>Approver of</label>
                                        <select class="select2 form-control" name="approver_of[]" multiple>
                                            @if(count($employeeToUpdate->divisionsApprover) > 0)
                                                @foreach($businessUnits as $businessUnit)
                                                    <option
                                                        @foreach($employeeToUpdate->divisionsApprover as $approver)
                                                        {{$approver->id == $businessUnit->id ? 'selected="Selected"':""}}
                                                        @endforeach
                                                        value="{{$businessUnit->id}}">{{$businessUnit->company->company_name.' - '.$businessUnit->bu_name}}
                                                    </option>
                                                @endforeach
                                            @else
                                                @foreach($businessUnits as $businessUnit)
                                                    <option value="{{$businessUnit->id}}">{{$businessUnit->company->company_name.' - '.$businessUnit->bu_name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer" id="bu-form-footer">
                                <button class="btn btn-success btn-sm" type="submit"><i class="la la-paper-plane-o"></i> Update Employee </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
