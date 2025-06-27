@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/animate/animate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/custom/vouchers.css')}}">
@endsection
@section('body')
    <div class="content-body">
        <section id="configuration">
            <div class="row justify-content-center">
                <div class="col-10">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Pending Approvals</h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                </ul>
                            </div>
                        </div>
                            <div class="card-body card-dashboard table-responsive w-100 overflow-auto">
                                <table class="table table-bordered row-grouping">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Category</th>
                                        <th>Charge To</th>
                                        <th>Date</th>
                                        <th>Employee</th>
                                        <th>Current Status</th>
                                        <th>Contains</th>
                                        <th>Total Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($employee->vouchersRequireApproval as $voucher)
                                        <tr>
                                            <td><a href="{{URL::to('voucher/details'.'/'.$voucher->id)}}">{{$voucher->id}}</a></td>
                                            <td>Claims</td>
                                            <td>{{$voucher->charge_to_cost_center}}<br></td>
                                            <td data-sort="{{strtotime($voucher->submission_date)}}">{{date('M d, Y', strtotime($voucher->submission_date))}}</td>
                                            <td>{{$voucher->employee->employee_name}}</td>
                                            <td>
                                                @foreach($voucher->voucherStatus as $status)
                                                    @if(isset($status->pivot->approved))
                                                        @if($status->pivot->approved == 1)
                                                            <p class="badge badge-pill bg-success mb-0">Approved by {{$status->id == $employee->id ? 'You':$status->employee_name}}</p> <br>
                                                        @elseif($status->pivot->approved == 0)
                                                            <p class="badge badge-pill bg-danger mb-0">Rejected by {{$status->id == $employee->id ? 'You':$status->employee_name}}</p> <br>
                                                        @endif
                                                    @else
                                                        <p class="badge badge-pill bg-warning mb-0">Pending approval from {{$status->id == $employee->id ? 'You':$status->employee_name}}</p> <br>
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                <ul style="list-style: decimal; margin-left: -25px">
                                                    @foreach($voucher->categories($voucher->id) as $item)
                                                        <li>{{$item->category_name}}</li>
                                                        @if($voucher->receiptNotProvided($voucher->id, $item->id) > 0)
                                                            <span class="badge badge-danger">No Receipts</span>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td><small>PKR </small>{{number_format($voucher->totalAmount($voucher->id), 0)}}</td>
                                            <td>
                                                @if($employee->user_name == 'medicines-mother-admin')
                                                <a href="{{URL::to('voucher/approve-medicines-mother'.'/'.$voucher->id)}}"><i class="la la-check-circle success"></i></a>
                                                @else
                                                <a href="{{URL::to('voucher/approve'.'/'.$voucher->id)}}"><i class="la la-check-circle success"></i></a>
                                                @endif
                                                <a class="reject" data-toggle="modal" data-target="#reject{{$voucher->id}}"><i class="la la-times-circle danger"></i></a>
                                            </td>
                                            <div class="modal animated shake text-left" id="reject{{$voucher->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel10" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger white">
                                                            <h4 class="modal-title white" id="myModalLabel10">Reject</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form method="post" action="{{URL::to('voucher/reject'.'/'.$voucher->id)}}">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                            <div class="modal-body">
                                                                <h5>Are you sure you want to reject this claim ?</h5>
                                                                <hr>
                                                                <p>Rejecting this claim will reject the previous approvals of this claim as well</p>
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
                                    @foreach($employee->travelOrdersRequireApproval as $voucher)
                                        <tr>
                                            <td><a href="{{URL::to('travel-order/details'.'/'.$voucher->id)}}">{{$voucher->id}}</a></td>
                                            <td>Travel Orders</td>
                                            <td>{{$voucher->charge_to_cost_center}}</td>
                                            <td data-sort="{{strtotime($voucher->submission_date)}}">{{date('M d, Y', strtotime($voucher->submission_date))}}</td>
                                            <td>{{$voucher->employee->employee_name}}</td>
                                            <td>
                                                @foreach($voucher->voucherStatus as $status)
                                                    @if(isset($status->pivot->approved))
                                                        @if($status->pivot->approved == 1)
                                                            <p class="badge badge-pill bg-success mb-0">Approved by {{$status->id == $employee->id ? 'You':$status->employee_name}}</p> <br>
                                                        @elseif($status->pivot->approved == 0)
                                                            <p class="badge badge-pill bg-danger mb-0">Rejected by {{$status->id == $employee->id ? 'You':$status->employee_name}}</p> <br>
                                                        @endif
                                                    @else
                                                        <p class="badge badge-pill bg-warning mb-0">Pending approval from {{$status->id == $employee->id ? 'You':$status->employee_name}}</p> <br>
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                <ul style="list-style: decimal; margin-left: -25px">
                                                    @foreach($voucher->categories($voucher->id) as $item)
                                                        <li>{{$item->category_name}}</li>
                                                        @if($voucher->receiptNotProvided($voucher->id, $item->id) > 0)
                                                            <span class="badge badge-danger">No Receipts</span>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td><small>PKR </small>{{number_format($voucher->totalAmount($voucher->id), 0)}}</td>
                                            <td>
                                                <a href="{{URL::to('travel-order/approve'.'/'.$voucher->id)}}"><i class="la la-check-circle success"></i></a>
                                                <a class="reject" data-toggle="modal" data-target="#reject{{$voucher->id}}"><i class="la la-times-circle danger"></i></a>
                                            </td>
                                            <div class="modal animated shake text-left" id="reject{{$voucher->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel10" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger white">
                                                            <h4 class="modal-title white" id="myModalLabel10">Reject</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form method="post" action="{{URL::to('travel-order/reject'.'/'.$voucher->id)}}">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                            <div class="modal-body">
                                                                <h5>Are you sure you want to reject this voucher ?</h5>
                                                                <hr>
                                                                <p>Rejecting this voucher will reject the previous approvals of this voucher as well</p>
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
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('footer')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            $('.row-grouping').DataTable({
                "columnDefs": [
                    { "visible": false, "targets": 1 },
                ],
                "order":[['1', 'desc']],
                "displayLength": 10,
                "drawCallback": function ( settings ) {
                    var api = this.api();
                    var rows = api.rows( {page:'current'} ).nodes();
                    var last=null;

                    api.column(1, {page:'current'} ).data().each( function ( group, i ) {
                        if ( last !== group ) {
                            if(group == 'open'){
                                $(rows).eq( i ).before(
                                    '<tr class="group bg-success"><td colspan="8">'+group+'</td></tr>'
                                );
                            }
                            else{
                                $(rows).eq( i ).before(
                                    '<tr class="group bg-success"><td colspan="8">'+group+'</td></tr>'
                                );
                            }
                            last = group;
                        }
                    } );
                }
            });
        });
    </script>
@endsection