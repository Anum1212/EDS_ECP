{{--*/ $departments = \App\Department::with('businessUnit', 'businessUnit.company')->whereHas('businessUnit.company', function($query) use ($employee){$query->where('companies.id', '=', $employee->department->businessUnit->company->id);})->get(); /*--}}
<div class="content-body">
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <div class="card" style="overflow: auto;">
                    <div class="card-header">
                        <h4 class="card-title">Departments</h4>
                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration">
                                <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Cost Center</th>
                                    <th>Department Name</th>
                                    <th>Classification</th>
                                    <th>Business Unit</th>
                                    <th>Employees</th>
                                    <th>Vouchers</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($departments as $department)
                                    <tr>
                                        <td>{{$department->businessUnit->company->company_name}}</td>
                                        <td>{{$department->cost_center}}</td>
                                        <td>{{$department->department_name}}</td>
                                        <td>{{$department->classification}}</td>
                                        <td>{{$department->businessUnit->bu_name}}</td>
                                        <td><span class="badge badge-success">{{count($department->employees)}}</span></td>
                                        <td><span class="badge badge-primary">{{count($department->vouchers)}}</span></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>