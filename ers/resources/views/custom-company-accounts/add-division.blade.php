{{--*/ $approvers = \App\Employee::with('department', 'department.businessUnit', 'divisionsApprover')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.id', '=', $employee->department->businessUnit->company->id);})->get(); /*--}}
{{--*/ $companies = \App\Company::where('id', '=', $employee->department->businessUnit->company->id)->get(); /*--}}
<div class="content-body">
    <section id="form-control-repeater">
        <form action="{{URL::to('division/add')}}" method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="file-repeater">Division Information</h4>
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
                                    <div class="form-group col-md-6 mb-2">
                                        <label>Select Company</label>
                                        <select class="select2 form-control" name="company" required>
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}">{{$company->company_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6 mb-2">
                                        <label>Division Name</label>
                                        <input type="text" class="form-control" value="{{Input::old('division_name')}}" name="division_name" required>
                                    </div>
                                    <div class="form-group col-md-12 mb-2">
                                        <label>Select Approvers</label>
                                        <select class="select2 form-control" name="approvers[]" multiple required>
                                            @foreach($approvers as $approver)
                                                <option value="{{$approver->id}}">{{$approver->employee_number. ' - '.$approver->employee_name.' / '.$approver->department->businessUnit->company->company_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer" id="bu-form-footer">
                                <button class="btn btn-success btn-sm" type="submit"><i class="la la-paper-plane-o"></i> Submit </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>