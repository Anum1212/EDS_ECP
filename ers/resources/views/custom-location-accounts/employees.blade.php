{{--*/ $employees = \App\Employee::with('department', 'department.businessUnit', 'divisionsApprover')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})->get(); /*--}}
<div class="content-body">
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <div class="card" style="overflow: auto;">
                    <div class="card-header">
                        <h4 class="card-title">Employees</h4>
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
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Designation</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Grade</th>
                                    <th>CNIC</th>
                                    <th>Account #</th>
                                    <th>Joining Date</th>
                                    <th>Level</th>
                                    <th>Company</th>
                                    <th>Division</th>
                                    <th>Department</th>
                                    <th>Approver of</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($employees as $row)
                                    <tr>
                                        <td><a href="{{URL::to('employee/update'.'/'.$row->id)}}">{{$row->employee_number}}</a></td>
                                        <td>{{$row->employee_name}}</td>
                                        <td>{{$row->designation}}</td>
                                        <td>{{$row->email}}</td>
                                        <td>{{$row->mobile}}</td>
                                         @if(isset($row->employeeGrade->primary_name))
                                        <td>{{trim(substr($row->employeeGrade->primary_name, 6,6))}}</td>
                                        @else 
                                        <td>{{trim(substr($row->grade, 6,6))}}</td>
                                        @endif
                                        <td>{{$row->cnic}}</td>
                                        <td>{{count($row->bankAccounts)>0?$row->bankAccounts[0]->pivot->account_number:""}}</td>
                                        <td>{{date('M d, Y', strtotime($row->date_of_joining))}}</td>
                                        <td>{{$row->level}}</td>
                                        <td>{{$row->department->businessUnit->company->company_name}}</td>
                                        <td>{{$row->department->businessUnit->bu_name}}</td>
                                        <td>{{$row->department->department_name}}</td>
                                        <td>
                                            @foreach($row->divisionsApprover as $division)
                                                {{$division->bu_name}}
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>