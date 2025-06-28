@extends('layouts.ers-layout')

@section('header')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/custom/site.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/daterange/daterangepicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/custom/daterangepicker.css') }}">
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
                            @if (isset($generateReport))
                                <button type="button" class="btn btn-sm btn-primary float-right mt-1" data-toggle="modal"
                                    data-target="#report">Generate Report</button>
                                {{-- <a href="{{ URL::to($generateReport) }}" type="button" class="btn btn-sm btn-primary float-right mt-1"  data-toggle="modal" data-target="#report">Generate Report</a> --}}
                            @endif
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-content collapse show">
                            <div class="card-body card-dashboard">
                                <table class="table table-striped table-bordered table-hover zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Employee</th>
                                            <th>Current Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($messBookings as $messBooking)
                                            <tr>
                                                @if (isset($userType))
                                                    @if ($userType == 'employee')
                                                        <td> <a
                                                                href="{{ URL::to($forwardingURL . $messBooking->id . '/employee') }}">{{ $messBooking->counter }}</a>
                                                        </td>
                                                    @elseif($userType == 'approver')
                                                        <td> <a
                                                                href="{{ URL::to($forwardingURL . $messBooking->id . '/approver') }}">{{ $messBooking->counter }}</a>
                                                        </td>
                                                    @endif
                                                @endif
                                                <td data-sort="{{ strtotime($messBooking->created_at) }}">
                                                    {{ date('M d, Y', strtotime($messBooking->created_at)) }}</td>
                                                <td>
                                                    @if ($messBooking->employee)
                                                        {{-- Check if the employee relationship exists --}}
                                                        {{ $messBooking->employee->employee_name }}
                                                    @endif
                                                </td>
                                                <td>{{ $messBooking->status }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @if (isset($generateReport))
        <div class="modal text-left" id="report" tabindex="-1" role="dialog" aria-labelledby="myModalLabel10"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h4 class="modal-title white" id="myModalLabel10">Select Date Range</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="{{ URL::to($generateReport) }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="modal-body">
                            <div class="row form-group">
                                <div class="col-md-3 mt-1">
                                    <label for="dateRange">Date Range:</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control custom-input date-range-picker"
                                        name="dateRange" id="dateRange" required>
                                </div>
                            </div>

                            <!-- Optional: Add status filter -->
                            <div class="row form-group">
                                <div class="col-md-3 mt-1">
                                    <label for="status_filter">Status:</label>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control" name="status_filter" id="status_filter">
                                        <option value="all">All Bookings</option>
                                        <option value="approved">Approved Only</option>
                                        <option value="pending">Pending Only</option>
                                        <option value="rejected">Rejected Only</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-outline-primary">Generate Report</button>
                            <button type="button" class="btn grey btn-outline-secondary"
                                data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('footer')
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/js/scripts/tables/datatables/datatable-basic.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/pickers/dateTime/moment-with-locales.min.js') }}" type="text/javascript">
        < script src = "{{ asset('app-assets/vendors/js/pickers/daterange/daterangepicker.js') }}"
        type = "text/javascript" >
    </script>
    <script src="{{ asset('app-assets/js/scripts/custom/daterangepicker.js') }}" type="text/javascript"></script>
    <script>
        $('#dateRange').daterangepicker({
            // singleDatePicker: true,
            timePicker: false,
            locale: {
                format: 'YYYY/MM/DD' // Display format for date and time
            },
            opens: 'center',
            autoApply: true,
            autoUpdateInput: true,
        });
    </script>
@endsection
