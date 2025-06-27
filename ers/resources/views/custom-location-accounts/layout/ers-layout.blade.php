{{--*/ 
    $employee_id = Session::get('id');
    $employee = \App\Employee::find($employee_id);
    if($employee->location == 'Karachi'){
    $inProcessVouchers = \App\Voucher::with('department.businessUnit.company','employee')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})
    ->whereHas('employee',function($query) use ($employee){
    $query->whereIn('location',['Karachi']);
    })
    ->whereIn('status', ['In Process'])->whereNull('is_travel_order')->count();
    }
    else{
    $inProcessVouchers = \App\Voucher::with('department.businessUnit.company','employee')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})
    ->whereHas('employee',function($query) use ($employee){
    $query->whereNotIn('location',['Karachi']);
    })
    ->whereIn('status', ['In Process'])->whereNull('is_travel_order')->count();
    }
    
    
    /*--}}
{{--*/
     $employee_id = Session::get('id');
    $employee = \App\Employee::find($employee_id);
    if($employee->location == 'Karachi'){
    $inProcessTravelOrders = \App\Voucher::with('department.businessUnit.company','employee')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})
    ->whereHas('employee',function($query) use ($employee){
    $query->whereIn('location',['Karachi']);
    })->whereIn('status', ['In Process'])->where('is_travel_order', '=', 1)->count();
    }
    else{
    $inProcessTravelOrders = \App\Voucher::with('department.businessUnit.company','employee')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})
    ->whereHas('employee',function($query) use ($employee){
    $query->whereNotIn('location',['Karachi']);
    })->whereIn('status', ['In Process'])->where('is_travel_order', '=', 1)->count();
    }
    /*--}}
{{--*/
    $employee_id = Session::get('id');
    $employee = \App\Employee::find($employee_id);
    if($employee->location == 'Karachi'){
    $documentsTransitVouchers = \App\Voucher::with('department.businessUnit.company','employee')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})
    ->whereHas('employee',function($query) use ($employee){
    $query->whereIn('location',['Karachi']);
    })
    ->whereIn('status', ['Approved - Documents in transit'])->count(); 
    }
    else{
    $documentsTransitVouchers = \App\Voucher::with('department.businessUnit.company','employee')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})
    ->whereHas('employee',function($query) use ($employee){
    $query->whereNotIn('location',['Karachi']);
    })
    ->whereIn('status', ['Approved - Documents in transit'])->count(); 
    }
    /*--}}
<li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown"><i class="la la-cogs"></i><span>Administration</span></a>
    <ul class="dropdown-menu">
        <li data-menu="" class="{{$path == 'vouchers/status/update' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('vouchers/status/update')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Voucher Documents Status <span class="badge bg-danger white">{{$documentsTransitVouchers}}</span></a>
        </li>
        <li data-menu="" class="{{$path == 'process/vouchers' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('process/vouchers')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Process Vouchers <span class="badge bg-danger white">{{$inProcessVouchers}}</span></a>
        </li>
        <li data-menu="" class="{{$path == 'process/travel-orders' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('process/travel-orders')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Process Travel Orders <span class="badge bg-danger white">{{$inProcessTravelOrders}}</span></a>
        </li>
        <hr>
        <li data-menu="" class="{{$path == 'processed/vouchers' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('processed/vouchers')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Processed Vouchers</a>
        </li>
        <li data-menu="" class="{{$path == 'rejected/vouchers' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('rejected/vouchers')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Rejected Vouchers</a>
        </li>
        <hr>
        <li data-menu="" class="{{$path == 'extract/stats' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('extract/stats')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Extract Stats</a>
        </li>
    </ul>
</li>