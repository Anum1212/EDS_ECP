{{--*/ $clients = \App\Employee::with('department', 'department.businessUnit', 'department.businessUnit.company')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.group_name','=',$employee->department->businessUnit->company->group_name);})->get(); /*--}}
<div class="form-group col-md-12 mb-2">
    <label>Select Employee</label>
    <select class="form-control select2" name="employee[]" data-placeholder="Select" multiple>
        @if(count($clientFilters) > 0)
            @foreach($clients as $client)
                <option
                        @foreach($clientFilters as $filter)
                        {{$filter == $client->id ? 'selected':""}}
                        @endforeach
                        value="{{$client->id}}">{{$client->employee_number.' - '.$client->employee_name.' ('.$client->department->businessUnit->company->company_name.' - '.$client->department->department_name.')'}}
                </option>
            @endforeach
        @else
            <option></option>
            @foreach($clients as $client)
                <option value="{{$client->id}}">{{$client->employee_number.' - '.$client->employee_name.' ('.$client->department->businessUnit->company->company_name.' - '.$client->department->department_name.')'}}</option>
            @endforeach
        @endif
    </select>
</div>