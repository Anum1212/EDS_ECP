{{--*/ 
     $employee_id = Session::get('id');
    $employee = \App\Employee::find($employee_id);
    if($employee->location == 'Karachi'){
    $vouchers = \App\Voucher::where('status', '=', 'Approved - Documents in transit')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})
    ->whereHas('employee',function($query) use ($employee){
    $query->whereIn('location',['Karachi']);
    })
    ->get();}
    else{
    $vouchers = \App\Voucher::where('status', '=', 'Approved - Documents in transit')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.group_name', '=', $employee->department->businessUnit->company->group_name);})
    ->whereHas('employee',function($query) use ($employee){
    $query->whereNotIn('location',['Karachi']);
    })
    ->get();
    }
    /*--}}
<div class="content-body">
    <section id="form-control-repeater">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="file-repeater">Select Voucher Number <h4>To Update the Documents Status</h4><small><a href="{{URL::to('vouchers/status/update/all')}}">Click here to check all vouchers in this status</a></small></h4>
                        <a class="heading-elements-toggle"><i class="la la-ellipsis-h font-medium-3"></i></a>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body">
                            <div class="form row">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="form-group col-md-4 mb-2">
                                    <select name="voucher" class="form-control voucher select2" data-placeholder="Select Voucher #">
                                        <option></option>
                                        @foreach($vouchers as $voucher)
                                            <option value="{{$voucher->id}}">{{$voucher->department->businessUnit->company->company_name.' / '.$voucher->id}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12" id="voucher-details">

            </div>
            <div class="modal animated rubberBand text-left" id="error" tabindex="-1" role="dialog" aria-labelledby="myModalLabel10" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger white">
                            <h4 class="modal-title white">OH !</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Voucher you are trying to access may have already been processed or is not approved yet or does not exist.<br><br>Please try again later.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>