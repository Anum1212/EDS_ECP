{{--*/ $divisions = \App\Business_Unit::with('company')->get(); /*--}}
<div class="form-group col-md-12 mb-2">
    <label>Select Divisions</label>
    <select class="form-control select2" name="divisions[]" data-placeholder="Select" multiple>
        @if(count($divisionFilters) > 0)
            @foreach($divisions as $division)
                <option
                    @foreach($divisionFilters as $filter)
                    {{$filter == $division->id ? 'selected':""}}
                    @endforeach
                    value="{{$division->id}}">{{$division->bu_name.' / '.$division->company->company_name}}</option>
                </option>
            @endforeach
        @else
            <option></option>
            @foreach($divisions as $division)
                <option value="{{$division->id}}">{{$division->bu_name.' / '.$division->company->company_name}}</option>
            @endforeach
        @endif
    </select>
</div>