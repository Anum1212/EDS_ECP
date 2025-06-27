{{--*/ $companies = \App\Company::all(); /*--}}
<div class="form-group col-md-12 mb-2">
    <label>Select Company</label>
    <select class="form-control select2 company" name="company" data-placeholder="Select">
        <option></option>
        @foreach($companies as $company)
            @if($companyFilter)
                <option value="{{$company->id}}" {{$companyFilter == $company->id ? 'selected':""}}>{{$company->company_name}}</option>
            @else
                <option value="{{$company->id}}">{{$company->company_name}}</option>
            @endif
        @endforeach
    </select>
</div>