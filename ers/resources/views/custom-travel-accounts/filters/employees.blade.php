{{--*/ $clients = \App\Employee::with('department', 'department.businessUnit', 'department.businessUnit.company')->get(); /*--}}
<div class="form-group col-md-12 mb-2">
    <label>Select Employee</label>
    <select class="form-control select2 employee" name="employee" data-placeholder="Select">
        <option></option>
        @foreach($clients as $client)
            <option value="{{$client->id}}">{{$client->employee_number.' - '.$client->employee_name}}</option>
        @endforeach
    </select>
</div>