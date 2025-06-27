@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/animate/animate.css')}}">
    <style>
        .content {
            background-color: #414042; /* Grey background */
        }
    </style>
@endsection
@section('body')
    <div class="content-body">
        <section id="configuration">
            <div class="row justify-content-center">
                <div class="col-10">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Vouchers Declined by You</h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                </ul>
                            </div>
                        </div>
                            <div class="card-body card-dashboard table-responsive w-100 overflow-auto">
                                <table class="table table-striped table-bordered row-grouping">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Category</th>
                                        <th>Charged To</th>
                                        <th>Date</th>
                                        <th>Employee</th>
                                        <th>Current Status</th>
                                        <th>Voucher Status</th>
                                        <th>Contains</th>
                                        <th>Total Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($employee->vouchersDeclined as $voucher)
                                        <tr>
                                            <td><a href="{{URL::to('voucher/details'.'/'.$voucher->id)}}">{{$voucher->id}}</a></td>
                                            <td>Vouchers</td>
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
                                            <td>{{$voucher->status}}</td>
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
                                    @endforeach
                                    @foreach($employee->travelOrdersDeclined as $voucher)
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
                                            <td>{{$voucher->status}}</td>
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