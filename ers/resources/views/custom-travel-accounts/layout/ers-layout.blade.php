{{--*/ $inProcessVouchers = \App\Voucher::with('department.businessUnit.company')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})->whereIn('status', ['In Process'])->whereNull('is_travel_order')->count(); /*--}}
{{--*/ $inProcessTravelOrders = \App\Voucher::with('department.businessUnit.company')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})->whereIn('status', ['In Process'])->where('is_travel_order', '=', 1)->count(); /*--}}
{{--*/ $documentsTransitVouchers = \App\Voucher::with('department.businessUnit.company')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})->whereIn('status', ['Approved - Documents in transit'])->count(); /*--}}
<li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown"><i class="la la-cogs"></i><span>Reports</span></a>
    <ul class="dropdown-menu">
        <li data-menu="" class="{{$path == 'travel-order/report' ? 'active':""}}">
            <a class="dropdown-item" href="{{URL::to('travel-order/report')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Travel Order Report</a>
        </li>
    </ul>
</li>