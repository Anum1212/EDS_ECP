{{--*/ $vouchers = \App\Voucher::where('status', '=', 'Approved - Documents in transit')->whereHas('department.businessUnit.company', function($query) use ($employee){$query->where('companies.id', '=', $employee->department->businessUnit->company->id);})->get(); /*--}}
<div class="content-body">
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Update Voucher Documents Status</h4>
                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration documents-table">
                                <thead>
                                <tr>
                                    <th colspan="7" class="text-center">Voucher Details</th>
                                    <th colspan="2" class="text-center">Documents</th>
                                </tr>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Department</th>
                                    <th>Employee</th>
                                    <th>Current Status</th>
                                    <th>Contains</th>
                                    <th>Total Amount</th>
                                    <th>Received</th>
                                    <th>Objection</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($vouchers as $voucher)
                                    <tr>
                                        <td><a href="{{URL::to('voucher/details'.'/'.$voucher->id)}}">{{$voucher->id}}</a></td>
                                        <td>{{date('M d, Y', strtotime($voucher->submission_date))}}</td>
                                        <td>{{$voucher->employee->department->department_name}}<br><small>{{$voucher->employee->department->businessUnit->company->company_name}}</small></td>
                                        <td>{{$voucher->employee->employee_name}}</td>
                                        <td>
                                            @foreach($voucher->voucherStatus as $status)
                                                @if(isset($status->pivot->approved))
                                                    @if($status->pivot->approved == 1)
                                                        <span class="badge badge-pill bg-success mb-0">Approved by {{$status->employee_name}}</span> <br>
                                                    @elseif($status->pivot->approved == 0)
                                                        <span class="badge badge-pill bg-danger mb-0">Rejected by {{$status->employee_name}}</span> <br>
                                                    @endif
                                                @else
                                                    <span class="badge badge-pill bg-warning mb-0">Pending approval from {{$status->employee_name}}</span> <br>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            <ul style="list-style: decimal; margin-left: -25px;">
                                                @foreach($voucher->categories($voucher->id) as $item)
                                                    <li>{{$item->category_name}}</li>
                                                    @if($voucher->receiptNotProvided($voucher->id, $item->id) > 0)
                                                        <span class="badge badge-danger">No Receipts</span>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td><small>PKR </small>{{number_format($voucher->totalAmount($voucher->id),0)}}</td>
                                        <td class="text-center"><a href="{{URL::to('voucher/documents/received'.'/'.$voucher->id)}}"><i class="la la-check-circle success"></i></a></td>
                                        <td class="text-center"><a class="reject" data-toggle="modal" data-target="#reject{{$voucher->id}}"><i class="la la-times-circle danger"></i></a></td>
                                        <div class="modal animated shake text-left" id="reject{{$voucher->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel10" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger white">
                                                        <h4 class="modal-title white" id="myModalLabel10">Reject</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form method="post" action="{{URL::to('documents/objection'.'/'.$voucher->id)}}">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <div class="modal-body">
                                                            <h5>Are you sure you want to raise document objection on this voucher ?</h5>
                                                            <hr>
                                                            <label>Rejection Comments</label>
                                                            <textarea rows="5" class="form-control" name="rejection_comments" id="rejection_comments" maxlength="500" required>{{Input::old('rejection_comments')}}</textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-outline-danger">Reject!</button>
                                                            <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        <div class="card-footer text-right">
                            <button class="btn btn-success" id="updateStatus" disabled>Documents Received</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>