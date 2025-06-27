@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/custom/vouchers.css')}}">
@endsection
@section('body')
    <div class="content-body">
        <section id="configuration">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Approved Travel Orders</h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-content collapse show">
                            <div class="card-body card-dashboard">
                                <table class="table table-striped table-bordered zero-configuration">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Travel Duration</th>
                                        <th>Purpose</th>
                                        <th>Places to Visit</th>
                                        <th>Current Status</th>
                                        <th>Travel Order Status</th>
                                        <th>Total Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($employee->approvedTravelOrders as $travelOrder)
                                        <tr>
                                            <td><a href="{{URL::to('travel-order/details'.'/'.$travelOrder->id)}}">{{$travelOrder->id}}</a></td>
                                            <td>{{$travelOrder->travel_order_type}}</td>
                                            <td>{{date('M d, Y H:i', strtotime($travelOrder->date_from)).' to'}}<br>{{date('M d, Y H:i', strtotime($travelOrder->date_to))}}</td>
                                            <td>{{$travelOrder->purpose}}</td>
                                            <td>{{$travelOrder->places_to_visit}}</td>
                                            <td>
                                                @foreach($travelOrder->travelOrderStatus as $status)
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
                                            <td>{{$travelOrder->status}}</td>
                                            <td><small>PKR </small>{{number_format($travelOrder->amount,0)}}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
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
@endsection