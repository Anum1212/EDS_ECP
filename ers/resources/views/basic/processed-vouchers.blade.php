@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    <style>
        .content {
            background-color: #414042;
            /* Grey background for the content */
        }

        .table thead th {
            background-color: #e9ecef;
            /* Light gray background for table headers */
            color: #333;
            /* Darker text for contrast */
        }

        .badge {
            font-size: 0.875rem;
        }

        .table th,
        .table td {
            vertical-align: middle;
            padding: 0.75rem;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
            /* Light hover effect for table rows */
        }
    </style>
@endsection
@section('body')
    <div class="content-body">
        <section id="configuration">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Claims</h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body card-dashboard w-100 overflow-auto">
                            <table class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Employee #</th>
                                        <th>Bank</th>
                                        <th>Employee</th>
                                        <th>Cost Center</th>
                                        <th>Current Status</th>
                                        <th>Voucher Status</th>
                                        <th>Processed On</th>
                                        <th>Contains</th>
                                        <th>JV Number</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vouchers as $voucher)
                                        <tr>
                                            <td><a
                                                    href="{{ URL::to('voucher/details' . '/' . $voucher->id) }}">{{ $voucher->id }}</a>
                                            </td>
                                            <td>{{ date('M d, Y', strtotime($voucher->submission_date)) }}</td>
                                            <td>{{ $voucher->employee->employee_number }}</td>
                                            <!--<td>{{ $filterBank == 1 ? 'MCB' : 'SCB' }}</td>-->
                                            <td>{{ ($filterBank == 1 ? 'MCB' : $filterBank == 2) ? 'SCB' : 'Emirates NBD' }}
                                            </td>

                                            <td>{{ $voucher->employee->employee_name }}</td>
                                            <td>{{ $voucher->charge_to_cost_center }}</td>
                                            <td>
                                                @foreach ($voucher->voucherStatus as $status)
                                                    @if (isset($status->pivot->approved))
                                                        @if ($status->pivot->approved == 1)
                                                            <span class="badge badge-pill bg-success mb-0">Approved by
                                                                {{ $status->employee_name }}</span> <br>
                                                        @elseif($status->pivot->approved == 0)
                                                            <span class="badge badge-pill bg-danger mb-0">Rejected by
                                                                {{ $status->employee_name }}</span> <br>
                                                        @endif
                                                    @else
                                                        <span class="badge badge-pill bg-warning mb-0">Pending approval from
                                                            {{ $status->employee_name }}</span> <br>
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>{{ $voucher->status }}</td>
                                            <td>{{ date('M d, Y', strtotime($voucher->processed_at)) }}</td>
                                            <td>
                                                <ul style="list-style: decimal; margin-left: -25px;">
                                                    @foreach ($voucher->categories($voucher->id) as $item)
                                                        <li>{{ $item->category_name }}</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td>{{ count($voucher->processedVouchers) > 0 ? $voucher->processedVouchers[0]->jv_number : '' }}
                                            </td>
                                            <td><small>PKR
                                                </small>{{ number_format($voucher->totalAmount($voucher->id), 0) }}</td>
                                        </tr>
                                    @endforeach
                            </table>
                        </div>
                        @if ($filterStatus == 'Processed')
                            <div class="card-footer text-right">
                                <!--<a href="{{ URL::to('generate/export/formats' . '/' . $filterColumn . '/' . $from . '/' . $to) }}" class="btn btn-primary">Generate Export Formats</a>-->
                                <a href="{{ URL::to('generate/export/formats' . '/' . $filterBank . '/' . $filterColumn . '/' . $from . '/' . $to) }}"
                                    class="btn btn-primary">Generate Export Formats</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('footer')
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/js/scripts/tables/datatables/datatable-basic.js') }}" type="text/javascript">
    </script>
@endsection
