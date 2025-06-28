@extends(isset($layout) ? 'layouts.ers-layout' : 'layouts.base')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/animate/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/custom/vouchers.css') }}">
@endsection
@section('body')
    <div class="row">
        <!-- left div -->
        <div id="leftContentPanel" class="card col-12 col-lg-8 mr-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0" style="font-size: 30px;">
                    {{ $panelHeading }} </h4>
                <span class="badge bg-primary" style="font-size: 1.2rem;">
                    {{ $messBooking->id }}
                </span>
            </div>
            <div class="card-body">
                <div class="col-12 w-100 overflow-auto">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="text-right"><strong>Employee ID: </strong></td>
                                <td>{{ $initiator_employee_sf_details->employee_number }}</td>
                                <td class="text-right"><strong>Name: </strong></td>
                                <td>{{ $initiator_employee_sf_details->employee_name }}</td>
                            </tr>
                            <tr>
                                <td class="text-right"><strong>Grade: </strong></td>
                                <td>{{ $initiator_employee_sf_details->grade }}</td>
                                <td class="text-right"><strong>Designation: </strong></td>
                                <td>{{ $initiator_employee_sf_details->designation }}</td>
                            </tr>
                            <tr>
                                <td class="text-right"><strong>Business Unit: </strong></td>
                                <td>{{ $initiator_employee_sf_details->employee->department->BusinessUnit->bu_name }}</td>
                                <td class="text-right"><strong>Department: </strong></td>
                                <td>{{ $initiator_employee_sf_details->employee->department->department_name }}</td>
                            </tr>
                            <tr>
                                <td class="text-right"><strong>Company: </strong></td>
                                <td>{{ $initiator_employee_sf_details->employee->department->BusinessUnit->company->company_name }}
                                </td>
                                <td class="text-right"><strong>Cost Center: </strong></td>
                                <td>{{ $initiator_employee_sf_details->cost_center }}</td>
                            </tr>
                            <tr>
                                <td class="text-right"><strong>Line Manager: </strong></td>
                                <td>{{ $initiator_employee_sf_details->line_manager->employee->employee_name }}</td>
                                <td class="text-right"><strong>Cost Center Approver: </strong></td>
                                <td>{{ $initiator_employee_sf_details->BusinessUnit->bu_head->employee->employee_name }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="heading-elements">
                    @if ($messBooking->status == 'Submitted')
                        <p class="badge badge-pill bg-primary">{{ $messBooking->status }}</p>
                    @elseif($messBooking->status == 'Approved - Documents in Transit')
                        <p class="badge badge-pill bg-warning">{{ $messBooking->status }}</p>
                    @elseif($messBooking->status == 'In Process')
                        <p class="badge badge-pill bg-yellow">{{ $messBooking->status }}</p>
                    @elseif($messBooking->status == 'Processed')
                        <p class="badge badge-pill bg-success">{{ $messBooking->status }}</p>
                    @elseif($messBooking->status == 'Rejected')
                        <p class="badge badge-pill bg-danger">{{ $messBooking->status }}</p>
                    @endif
                </div>
                <table class="table table-striped table-bordered display no-wrap table-middle">
                    <thead class="header-t">
                        <tr>
                            <th>Booking</th>
                            <th>Type</th>
                            <th>BSP Head Count</th>
                            <th>Guest Head Count</th>
                            <th>Total Head Count</th>
                            <th>Date</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="taskrow">
                            <td>{{ $messBooking->booking_name }}</td>
                            <td>{{ $messBookingType }}</td>
                            <td>{{ number_format($messBooking->bsp_employee_count, 0) }}</td>
                            <td>{{ number_format($messBooking->guest_count, 0) }}</td>
                            <td>{{ number_format($messBooking->total_head_count) }}</td>
                            @if ($messBooking->booking_start_date == $messBooking->booking_end_date)
                                <td>{{ date('M d, Y', strtotime($messBooking->booking_start_date)) }}</td>
                            @else
                                <td>{{ date('M d, Y', strtotime($messBooking->booking_start_date)) }} -
                                    {{ date('M d, Y', strtotime($messBooking->booking_end_date)) }}</td>
                            @endif
                            <td>{{ substr($messBooking->booking_time, 0, -3) }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="card-body">
                    {{-- for employees --}}
                    @if (isset($submitURL) && $userType == 'employee' && $messBooking->status == 'Draft')
                        <a href="{{ URL::to($submitURL . '/' . $messBooking->id . '/employee') }}">
                            <button type="button" class="btn btn-success" style="padding: 5px 8px; font-size: 14px;">
                                <i class="la la-check-circle"></i> Send for Approval
                            </button>
                        </a>
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#delete"
                            style="padding: 5px 8px; font-size: 14px;">
                            <i class="la la-times-circle"></i> Delete
                        </button>
                        {{-- for approvers --}}
                    @elseif(isset($approveURL) && $userType == 'approver' && $messBooking->status == 'Submitted' || $messBooking->status == 'Pending')
                        <a href="{{ URL::to($approveURL . '/' . $messBooking->id) }}">
                            <button type="button" class="btn btn-success" style="padding: 5px 8px; font-size: 14px;">
                                <i class="la la-check-circle"></i> Approve
                            </button>
                        </a>
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#reject"
                            style="padding: 5px 8px; font-size: 14px;">
                            <i class="la la-times-circle"></i> Reject
                        </button>
                    @endif
                </div>
            </div>
            </section>
        </div>
    <!-- right side -->
    <div id="rightContentPanel" class="card col-12 col-lg-3 ml-5">
        <div class="card-body">
            <div class="text-center p-2">
                <h4>Approval Level(s)</h4>
                <h6>Your {{ $panelHeading }} requires level <span class="badge badge-pill"
                        style="background-color:#03a2dd;">
                        1
                    </span> approval</h6>
            </div>
            <hr>
            <h4>{{ $panelHeading }} History</h4>
            <div class="table-responsive">
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Time</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Created Status Row -->
                        <tr>
                            <td><span class="badge badge-info">Created</span></td>
                            <td>{{ date('M d, Y', strtotime($messBooking->created_at)) }} at
                                {{ date('H:i:s', strtotime($messBooking->created_at)) }}
                            </td>
                            <td>Initial creation of Y-Lunch</td>
                        </tr>
                        @if ($messBooking->status != 'Draft')
                            <tr>
                                <td>
                                    @if ($messBooking->status == 'Approved')
                                        <span class="badge badge-warning">Approved-In Transit</span>
                                    @elseif($messBooking->status == 'Rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @elseif($messBooking->status == 'Submitted')
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    {{ date('M d, Y', strtotime($messBooking->updated_at)) }} at
                                    {{ date('H:i:s', strtotime($messBooking->updated_at)) }}
                                </td>
                                <td>
                                    @if (isset($messBooking->approver_emp_number))
                                        @if ($messBooking->status == 'Approved')
                                            Approved by
                                            {{ $employee->employee_number == $messBooking->approver_emp_number ? 'Self' : $employee->employee_name }}
                                        @elseif ($messBooking->status == 'Rejected')
                                            Rejected by {{ $employee->employee_name }}
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    @if (isset($rejectURL))
        <div class="modal animated shake text-left" id="reject" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel10" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger white">
                        <h4 class="modal-title white" id="myModalLabel10">Reject</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="{{ URL::to($rejectURL . '/' . $messBooking->id) }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="modal-body">
                            <h5>Are you sure you want to reject this {{ $panelHeading }} ?
                            </h5>
                            <label>Rejection Comments</label>
                            <textarea rows="5" class="form-control" name="rejection_comments" id="rejection_comments" maxlength="500"
                                required>{{ Input::old('rejection_comments') }}</textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-outline-danger">Reject!</button>
                            <button type="button" class="btn grey btn-outline-secondary"
                                data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <div class="modal animated shake text-left" id="delete" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel10" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger white">
                    <h4 class="modal-title white" id="myModalLabel10">Delete</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Are you sure you want to delete this {{ $panelHeading }} ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                    @if (isset($deleteURL))
                        <a href="{{ URL::to($deleteURL . '/' . $messBooking->id) }}"><button type="button"
                                class="btn btn-outline-danger">Confirm Delete</button></a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer')
    <script>
        function printDetails() {
            $("#contentWrapper").print({
                noPrintSelector: ".dont-print",
            });
        }

        $('#make-copy').on('click', function() {
            $('#voucher-form').submit();
        })
    </script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/js/scripts/tables/datatables/datatable-basic.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/js/scripts/modal/components-modal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/js/scripts/pages/custom-project-details/project-task-list.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/jquery.knob.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/js/scripts/cards/card-statistics.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/export-word/js/FileSaver.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/export-word/js/jquery.wordexport.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/charts/echarts/echarts.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/print/jQuery.print.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/js/scripts/charts/echarts/pie-doughnut/doughnut-infographic.js') }}"
        type="text/javascript"></script>
@endsection
