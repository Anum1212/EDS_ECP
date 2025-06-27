@extends('layouts.ers-layout')

@section('header')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/custom/site.css') }}">
    <style>
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
                <div class="col-10">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title d-flex justify-content-between align-items-center">{{ $panelHeading }}
                            </h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-content collapse show">
                            <div class="card-body card-dashboard">
                                @if (isset($messBookings))
                                    <table class="table table-striped table-bordered table-hover zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Employee Number</th>
                                                <th>Employee Name</th>
                                                <th>Current Status</th>
                                                <th>Remarks</th>
                                                <th>Submission Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($messBookings as $messBooking)
                                                <tr>
                                                    <td>{{ $messBooking->counter }}</td>
                                                    <td>{{ $messBooking->employee_number }}</td>
                                                    <td>{{ $messBooking->employee_name }}</td>
                                                    <td>{{ $messBooking->status }}</td>
                                                    <td>{{ $messBooking->remarks }}</td>
                                                    <td data-sort="{{ strtotime($messBooking->created_at) }}">
                                                        {{ date('M d, Y', strtotime($messBooking->created_at)) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
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
