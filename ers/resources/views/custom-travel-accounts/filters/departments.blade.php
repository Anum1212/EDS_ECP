{{--*/ $departments = \App\Department::with('businessUnit', 'businessUnit.company')->get(); /*--}}
<div class="form-group col-md-12 mb-2">
    <label>Select Departments</label>
    <select class="form-control select2" name="departments[]" data-placeholder="Select" multiple>
        @if(count($departmentFilters) > 0)
            @foreach($departments as $department)
                <option
                    @foreach($departmentFilters as $filter)
                    {{$filter == $department->id ? 'selected':""}}
                    @endforeach
                    value="{{$department->id}}">{{$department->department_name.' / '.$department->businessUnit->bu_name}}</option>
                </option>
            @endforeach
        @else
            <option></option>
            @foreach($departments as $department)
                <option value="{{$department->id}}">{{$department->department_name.' / '.$department->businessUnit->bu_name}}</option>
            @endforeach
        @endif
    </select>
</div>