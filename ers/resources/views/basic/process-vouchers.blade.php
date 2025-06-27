@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/custom/vouchers.css')}}">
@endsection
@section('body')
    <div class="content-body">
        <section id="configuration">
            <div class="row justify-content-center">
                <div class="col-10">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Process Claims</h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                    <button class="btn btn-info btn-sm white select_all" onclick="selectAll()" id="select_all">Select All</button>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body card-dashboard w-100 overflow-auto">
                            <form method="post" action="{{URL::to('process/vouchers')}}">
                                <input name="_token" value="{{csrf_token()}}" hidden>
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>Employee</th>
                                            <th>Current Status</th>
                                            <th>Voucher Status</th>
                                            <th>Company</th>
                                            <th>Contains</th>
                                            <th>Total Amount</th>
                                            <th>Action Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($vouchers as $voucher)
                                            <tr>
                                                <td hidden><input name="vouchers[]" value="{{$voucher->id}}"></td>
                                                <td><a href="{{URL::to('voucher/details'.'/'.$voucher->id)}}">{{$voucher->id}}</a></td>
                                                <td>{{date('M d, Y', strtotime($voucher->submission_date))}}</td>
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
                                                <td>{{$voucher->status}}</td>
                                                <td>{{$voucher->company_name}}</td>
                                                <td>
                                                    <ul style="list-style: decimal; margin-left: -25px;">
                                                        @foreach($voucher->categories($voucher->id) as $item)
                                                            <li>{{$item->category_name}}</li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                                <td><small>PKR </small>
                                                    @if($voucher->totalApprovedAmount($voucher->id) > 0 && $voucher->totalAmount($voucher->id) > $voucher->totalApprovedAmount($voucher->id))
                                                        {{number_format($voucher->totalApprovedAmount($voucher->id),0)}}
                                                    @else
                                                        {{number_format($voucher->totalAmount($voucher->id),0)}}
                                                    @endif
                                                </td>
                                                <td><input type="checkbox" class="form-control action_check" name="actions[]" value="{{$voucher->id}}"></td>
                                            </tr>
                                        @endforeach
                                    </table>
                                <div class="card-footer text-right">
                                    <button class="btn btn-success" id="updateStatus">Process Claims</button>
                                </div>
                            </form>
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
        function selectAll()
        {
            var checkboxes = document.getElementsByClassName("action_check");
            for (i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = true;
            }
        }

    </script>
@endsection