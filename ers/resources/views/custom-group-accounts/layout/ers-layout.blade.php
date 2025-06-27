{{--*/ $inProcessVouchers = \App\Voucher::with('department.businessUnit.company')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})->whereIn('status', ['In Process'])->whereNull('is_travel_order')->count(); /*--}}
{{--*/ $inProcessTravelOrders = \App\Voucher::with('department.businessUnit.company')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})->whereIn('status', ['In Process'])->where('is_travel_order', '=', 1)->count(); /*--}}
{{--*/ $documentsTransitVouchers = \App\Voucher::with('department.businessUnit.company')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})->whereIn('status', ['Approved - Documents in transit'])->count(); /*--}}
<li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown"><i class="la la-users"></i><span>Employees</span></a>
    <ul class="dropdown-menu">
        <li data-menu="" class="{{$path == 'employee/add' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('employee/add')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Add Employee</a>
        </li>
        <li data-menu="" class="{{$path == 'employees' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('employees')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Employees</a>
        </li>
    </ul>
</li>
<li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown"><i class="la la-list"></i><span>Divisions</span></a>
    <ul class="dropdown-menu">
        <li data-menu="" class="{{$path == 'division/add' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('division/add')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Add Division</a>
        </li>
        <li data-menu="" class="{{$path == 'divisions' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('divisions')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Divisions</a>
        </li>
        <hr>
        <li data-menu="" class="{{$path == 'department/add' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('department/add')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Add Department</a>
        </li>
        <li data-menu="" class="{{$path == 'departments' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('departments')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Departments</a>
        </li>
    </ul>
</li>
<li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown"><i class="la la-cubes"></i><span>Categories</span></a>
    <ul class="dropdown-menu">
        <li data-menu="" class="{{$path == 'category/add' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('category/add')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Add Category</a>
        </li>
        <li data-menu="" class="{{$path == 'categories' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('categories')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Categories</a>
        </li>
        <hr>
        <li data-menu="" class="{{$path == 'gl-account/add' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('gl-account/add')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Add GL Account</a>
        </li>
        <li data-menu="" class="{{$path == 'gl-accounts' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('gl-accounts')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>GL Accounts</a>
        </li>
    </ul>
</li>
<li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown"><i class="la la-tag"></i><span>Rates</span></a>
    <ul class="dropdown-menu">
        <li data-menu="" class="{{$path == 'rate/add' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('rate/add')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Add Rate</a>
        </li>
        <li data-menu="" class="{{$path == 'rates' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('rates')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Rates</a>
        </li>
    </ul>
</li>
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