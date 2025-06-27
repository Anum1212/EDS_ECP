@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/custom/first-approval.css')}}">
@endsection
@section('body')
    <div id="contentWrapper" class="row">
        <div id="leftContentPanel" class="card col-12 col-lg-8 mr-5">
            <div class="content-body">
                <!-- left div -->
                <section id="description" class="card  mr-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0" style="font-size: 30px;">
                            {{ $panelHeading }} Details
                            @if (isset($voucher->child_id))
                                <br><strong>(Parent of 
                                    <a href="{{ URL::to($forwardingURL . '/' . 'details' . '/' . $voucher->child_id) }}">
                                        {{ $voucher->child_id }}
                                    </a>)</strong>
                            @endif
                            @if (isset($voucher->parent_id))
                                <br><strong>(Child of 
                                    <a href="{{ URL::to($forwardingURL . '/' . 'details' . '/' . $voucher->parent_id) }}">
                                        {{ $voucher->parent_id }}
                                    </a>)</strong>
                            @endif
                        </h4>
                    
                        <span class="badge bg-primary" style="font-size: 1.2rem;">
                            {{ $voucher->id }}
                        </span>
                    </div>
                    <div class="card-body px-4">
                        <div class="col-md-12 w-100 overflow-auto">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <!--<td class="text-right"><strong>Claim #: </strong></td>-->
                                        <!--<td>{{ $voucher->id }}</td>-->
                                        <td class="text-right"><strong>Cost Center: </strong></td>
                                        <td>{{ $voucher->charge_to_cost_center }}</td>
                                        <td class="text-right"><strong>Submission Date: </strong></td>
                                        <td>{{ $voucher->submission_date }}</td>
                                        <!--<td class="text-right"><strong> Claim Type: </strong></td>-->
                                        <!--<td>{{ $voucher->voucher_item->category->category_name }}</td>-->
                                    </tr>
                                    <!--<tr>-->
                                    <!--    <td class="text-right"><strong>Attachment: </strong></td>-->
                                    <!--    <td>-->
                                    <!--        {{ $voucher->voucher_item->receipt ? $voucher->voucher_item->receipt : $voucher->voucher_item->attachment }}-->
                                    <!--    </td>-->
                                    <!--    <td class="text-right"><strong>Amount: </strong></td>-->
                                    <!--    <td>{{ $voucher->voucher_item->amount }}</td>-->
                                    <!--</tr>-->
                                    <!--<tr>-->
                                    <!--    <td class="text-right"><strong>Description: </strong></td>-->
                                    <!--    <td>{{ $voucher->voucher_item->description }}</td>-->
                                    <!--    <td class="text-right"><strong>Status: </strong></td>-->
                                    <!--    <td>{{ $voucher->status }}</td>-->
                                    <!--</tr>-->
                                    <!--<tr>-->
                                        
                                    <!--</tr>-->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <div class="card-header mb-0 col-12 text-center">
                            <h3 class="display-5 text.dark">Total Amount (PKR)</h3>
                            <!--<h3 class="display-4 text.dark">{{ number_format($voucher->voucher_item->amount, 0) }}</h3>-->
                            @if (
                            $voucher->totalApprovedAmount($voucher->id) > 0 &&
                                    $voucher->totalAmount($voucher->id) > $voucher->totalApprovedAmount($voucher->id))
                                <h3 class="display-4 text.dark">{{ number_format($voucher->totalAmount($voucher->id), 0) }}
                                    ➡️ {{ number_format($voucher->totalApprovedAmount($voucher->id), 0) }}</h3>
                            @else
                                <h3 class="display-4 text.dark">{{ number_format($voucher->totalAmount($voucher->id), 0) }}
                                </h3>
                            @endif
                            <div class="progress progress-sm mt-1 mb-0">
                                <div class="progress-bar bg-dark" role="progressbar" style="width: 100%" aria-valuenow="100"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Approver Section -->
                    <div class="col-md-12 w-100 overflow-auto">
                        <form action="{{ URL::to($forwardingURL . '/first/approval/bu-wise' . '/' . $voucher->id) }}"
                            method="post">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group row">
                                <label for="inputPassword" class="col-sm-2 offset-1 col-form-label">Select approver from the
                                    list</label>
                                </h4>
                                <div class="col-sm-4">
                                    <select class="select2 form-control" name="approver" required>
                                        <option value="">Select</option>
                                        @foreach ($approvers as $approver)
                                            <option value="{{ $approver->id }}">
                                                {{ $approver->employee_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer" id="company-form-footer">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button class="btn btn-success white btn-sm" type="submit"><i
                                                class="la la-paper-plane-o"></i> Submit </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
            </div>
        </div>

        <div id="rightContentPanel" class="card col-12 col-lg-3 ml-5">
            <div class="sidebar-detached sidebar-right">
                <div class="sidebar-content card d-lg-block">
                    <div class="card-body">
                        <div class="text-center p-2">
                            <h4>Approval Level(s)</h4>
                            <h6>Your {{ $panelHeading }} requires level <span class="badge badge-pill"
                                    style="background-color:#03a2dd;">
                                    {{ count($voucher->requiresSecondApproval()) == 0 ? 1 : 2 }}
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
                                        <td>{{ date('M d, Y', strtotime($voucher->created_at)) }} at
                                            {{ date('H:i:s', strtotime($voucher->created_at)) }}
                                        </td>
                                        <td>Initial creation of the claim</td>
                                    </tr>

                                    <!-- Approval Status Rows -->
                                    @foreach ($voucher->voucherStatus as $status)
                                        <tr>
                                            <td>
                                                @if (isset($status->pivot->approved))
                                                    @if ($status->pivot->approved == 1)
                                                        <span class="badge badge-success">Approved</span>
                                                    @elseif($status->pivot->approved == 0)
                                                        <span class="badge badge-danger">Rejected</span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>{{ date('M d, Y', isset($status->pivot->approved) ? strtotime($status->pivot->updated_at) : strtotime($status->pivot->created_at)) }}
                                                at
                                                {{ date('H:i:s', isset($status->pivot->approved) ? strtotime($status->pivot->updated_at) : strtotime($status->pivot->created_at)) }}
                                            </td>
                                            <td>
                                                @if (isset($status->pivot->approved))
                                                    @if ($status->pivot->approved == 1)
                                                        Approved by
                                                        {{ $status->employee_name == $employee->employee_name ? 'Self' : $status->employee_name }}
                                                    @elseif($status->pivot->approved == 0)
                                                        Rejected by {{ $status->employee_name }}
                                                    @endif
                                                @else
                                                    Sent to {{ $status->employee_name }} for approval
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    <!-- Additional Status Rows -->
                                    @if ($voucher->status == 'Approved - Documents in transit')
                                        <tr>
                                            <td><span class="badge badge-warning">Documents in Transit</span></td>
                                            <td colspan="2"></td>
                                        </tr>
                                    @endif

                                    @if ($voucher->status == 'In Process')
                                        <tr>
                                            <td><span class="badge badge-warning">In Process</span></td>
                                            <td colspan="2"></td>
                                        </tr>
                                    @endif

                                    @if (isset($voucher->processed_at))
                                        <tr>
                                            <td><span class="badge badge-success">Processed</span></td>
                                            <td>{{ date('M d, Y', strtotime($voucher->processed_at)) }} at
                                                {{ date('H:i:s', strtotime($voucher->processed_at)) }}
                                            </td>
                                            <!-- <td></td> -->
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('footer')
        <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}" type="text/javascript"></script>
    @endsection
