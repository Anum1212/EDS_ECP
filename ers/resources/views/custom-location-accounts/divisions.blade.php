{{--*/ $divisions = \App\Business_Unit::whereHas('company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})->get(); /*--}}
<div class="content-body">
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <div class="card" style="overflow: auto;">
                    <div class="card-header">
                        <h4 class="card-title">Divisions</h4>
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
                                    <th rowspan="2">Name</th>
                                    <th colspan="{{count($approvalLevels)}}" class="text-center">Approvers</th>
                                </tr>
                                <tr>
                                    @foreach($approvalLevels as $approvalLevel)
                                        <th>{{$approvalLevel->level_name}}</th>
                                    @endforeach
                                </tr>

                                </thead>
                                <tbody>
                                @foreach($divisions as $division)
                                    <tr>
                                        <td>{{$division->bu_name}}<br><small>{{$division->company->company_name}}</small></td>
                                        @foreach($approvalLevels as $approvalLevel)
                                            <td>
                                                @foreach($division->approvers as $approver)
                                                    @if($approver->level == $approvalLevel->level)
                                                        {{$approver->employee_name}}<br><small>{{$approver->department->businessUnit->company->company_name}}</small><br>
                                                    @else
                                                    @endif
                                                @endforeach
                                            </td>
                                        @endforeach
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