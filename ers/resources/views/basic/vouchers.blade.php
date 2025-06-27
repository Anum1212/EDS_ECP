@extends('layouts.ers-layout')

@section('header')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/custom/vouchers.css')}}">
@endsection

@section('body')
    <div class="content-body">
        <section id="configuration">
            <div class="row justify-content-center">
                <div class="col-10">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ $panelHeading }}</h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                </ul>
                            </div>
                        </div>
                            <div class="card-body card-dashboard w-100 overflow-auto">
                                <table class="table table-striped table-bordered table-hover zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>Employee</th>
                                            <th>Current Status</th>
                                            <th>Reimbursement Status</th>
                                            <th>Categories</th>
                                            <th>Total Amount</th>
                                            <th>Amount Approved</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($vouchers as $voucher)
                                        <tr>
                                            <td><a href="{{ URL::to($forwardingURL.'/details/'.$voucher->id) }}">{{ $voucher->id }}</a></td>
                                            <td data-sort="{{ strtotime($voucher->submission_date) }}">{{ date('M d, Y', strtotime($voucher->submission_date)) }}</td>
                                            <td>{{ $voucher->employee->employee_name }}</td>
                                            <td>
                                                @foreach($voucher->voucherStatus as $status)
                                                    @if(isset($status->pivot->approved))
                                                        @if($status->pivot->approved == 1)
                                                            <span class="badge badge-success">Approved by {{ $status->employee_name }}</span>
                                                        @elseif($status->pivot->approved == 0)
                                                            <span class="badge badge-danger">Rejected by {{ $status->employee_name }}</span>
                                                        @endif
                                                    @else
                                                        <span class="badge badge-warning">Pending approval from {{ $status->employee_name }}</span>
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>{{ $voucher->status }}</td>
                                            <td>
                                                <ul>
                                                    @foreach($voucher->categories($voucher->id) as $item)
                                                        <li>{{ $item->category_name }}</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td><small>PKR </small>{{ number_format($voucher->totalAmount($voucher->id), 0) }}</td>
                                            <td><small>PKR </small>{{ 
                                            $voucher->totalAmountApproved($voucher->id) ? $voucher->totalAmountApproved($voucher->id) : $voucher->totalAmount($voucher->id) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/js/scripts/tables/datatables/datatable-basic.js') }}" type="text/javascript"></script>
@endsection
