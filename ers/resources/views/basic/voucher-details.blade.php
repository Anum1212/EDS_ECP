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
            <div class="card-body">
                <div class="col-12 w-100 overflow-auto">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="text-right"><strong>Employee ID: </strong></td>
                                <td>{{ $employee_sf_data->employee_number }}</td>
                                <td class="text-right"><strong>Name: </strong></td>
                                <td>{{ $employee_sf_data->employee_name }}</td>
                            </tr>
                            <tr>
                                <td class="text-right"><strong>Grade: </strong></td>
                                <td>{{ $employee_sf_data->grade }}</td>
                                <td class="text-right"><strong>Designation: </strong></td>
                                <td>{{ $employee_sf_data->designation }}</td>
                            </tr>
                            <tr>
                                <td class="text-right"><strong>Business Unit: </strong></td>
                                <td>{{ $employee_sf_data->employee->department->BusinessUnit->bu_name }}</td>
                                <td class="text-right"><strong>Department: </strong></td>
                                <td>{{ $employee_sf_data->employee->department->department_name }}</td>
                            </tr>
                            <tr>
                                <td class="text-right"><strong>Company: </strong></td>
                                <td>{{ $employee_sf_data->employee->department->BusinessUnit->company->company_name }}
                                </td>
                                <td class="text-right"><strong>Cost Center: </strong></td>
                                <td>{{ $employee_sf_data->cost_center }}</td>
                            </tr>
                            <tr>
                                <td class="text-right"><strong>Bank: </strong></td>
                                <td>{{ $employee_sf_data->bank }}</td>
                                <td class="text-right"><strong>Account Number: </strong></td>
                                <td>{{ $employee_sf_data->account_number }}</td>
                            </tr>
                            <tr>
                                <td class="text-right"><strong>Line Manager: </strong></td>
                                <td>{{ isset($employee_sf_data->line_manager->employee->employee_name) ? $employee_sf_data->line_manager->employee->employee_name : 'Not Found' }}
                                </td>
                                <td class="text-right"><strong>Business Unit Approver: </strong></td>
                                <td>{{ isset($employee_sf_data->BusinessUnit->bu_head->employee->employee_name) ? $employee_sf_data->BusinessUnit->bu_head->employee->employee_name : 'Not Found' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    <div class="card-header mb-0 col-12 text-center">
                        <h3 class="display-5 text.dark">Total Amount (PKR)</h3>
                        @if (
                            $voucher->totalApprovedAmount($voucher->id) > 0 &&
                                $voucher->totalAmount($voucher->id) > $voucher->totalApprovedAmount($voucher->id))
                            <h3 class="display-4 text.dark">{{ number_format($voucher->totalAmount($voucher->id), 0) }}
                                ➡️ {{ number_format($voucher->totalApprovedAmount($voucher->id), 0) }}</h3>
                        @else
                            <h3 class="display-4 text.dark">{{ number_format($voucher->totalAmount($voucher->id), 0) }}
                            </h3>
                        @endif
                        @if (
                            $voucher->is_travel_order == 1 &&
                                $voucher->totalAmount($voucher->id) == 0 &&
                                $voucher->totalAmountForex($voucher->id) > 0)
                            <small>PKR amount will be calculated after the confirmation of Forex Rate by
                                <br><strong>Accounts Department</strong></small>
                        @endif
                        @if ($voucher->advanceTotalAmount($voucher->id))
                            <span>Advance Amount<br>
                                <h3>PKR {{ number_format($voucher->advanceTotalAmount($voucher->id), 0) }}</h3>
                            </span>
                        @endif
                        <div class="progress progress-sm mt-1 mb-0">
                            <div class="progress-bar bg-dark" role="progressbar" style="width: 100%" aria-valuenow="100"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
                @if (isset($voucher->currentApprover[0]) &&
                        ($voucher->currentApprover[0]->user_name == 'pl-medical-officer' ||
                            $voucher->currentApprover[0]->user_name == 'tpf-medical-officer'))
                    <p class="danger"><b>**</b>Please enter the amount that will be reimburseed to the Employee</p>
                    <form method="post"
                        action="{{ URL::to($forwardingURL . '/' . 'approve-medical-opd' . '/' . $voucher->id) }}">
                @endif
                <div class="col-12 w-100 overflow-auto">
                    <table class="table">
                        <thead class="header-t">
                            <tr>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Description</th>
                                @foreach ($voucher->categories($voucher->id) as $category)
                                    @if (
                                        $category->category_name == 'Foreign - DA' ||
                                            $category->category_name == 'Local - DA (via Air)' ||
                                            $category->category_name == 'Local - DA')
                                        <th>Personal Days</th>
                                        <th>Eligible DA</th>
                                        <th>Departure</th>
                                        <th>Arrival</th>
                                    @endif
                                @endforeach
                                <th>Amount</th>
                                @foreach ($voucher->categories($voucher->id) as $category)
                                    @if ($category->category_name == 'Medicines - Mother')
                                        <th>Approved Amount (PKR)</th>
                                    @endif
                                @endforeach
                                @foreach ($voucher->categories($voucher->id) as $category)
                                    @if ($category->category_name == 'Fuel - Receipts' || $category->category_name == 'Fuel - Receipts')
                                        <th>Liters</th>
                                    @endif
                                @endforeach
                                @foreach ($voucher->categories($voucher->id) as $category)
                                    @if (
                                        $category->category_name == 'Fuel - Mileage (within city)' ||
                                            $category->category_name == 'Fuel - Mileage (out of city)' ||
                                            $category->category_name == 'Fuel - Mileage')
                                        <th>KMs</th>
                                        <th>From</th>
                                        <th>To</th>
                                    @endif
                                @endforeach
                                <th>Attachment</th>
                                <th>Receipt Provided</th>
                                @foreach ($voucher->categories($voucher->id) as $category)
                                    @if ($category->category_name == 'Medical - OPD')
                                        <th>Approved Amount (PKR)</th>
                                    @endif
                                @endforeach
                                
                                @foreach ($voucher->categories($voucher->id) as $category)
                                    @if ($category->category_name == 'Local - DA (via Air)' || $category->category_name == 'Local - DA')
                                        <th>Accommodation Required</th>
                                    @endif
                                @endforeach
                                @if (count($voucher->voucherStatus) == 0)
                                    <th>Delete</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($voucher->categories($voucher->id) as $category)
                                @foreach ($voucher->categoryItems($voucher->id, $category->id) as $item)
                                    <tr class="taskrow">
                                        <td>{{ $category->category_name }}</td>
                                        @if (isset($item->date_from) && isset($item->date_to))
                                            <td>{{ date('m/d/Y H:i', strtotime($item->date_from)) . ' - ' . date('m/d/Y H:i', strtotime($item->date_to)) }}
                                            </td>
                                        @else
                                            <td>{{ date('M d, Y', strtotime($item->date_from)) }}</td>
                                        @endif
                                        <!--<td>{{ date('M d, Y', strtotime($item->date_from)) }}</td>-->
                                        <td>{{ $item->description }}</td>
                                        @if (
                                            $category->category_name == 'Foreign - DA' ||
                                                $category->category_name == 'Local - DA (via Air)' ||
                                                $category->category_name == 'Local - DA')
                                            @if (isset($item->date_to) && isset($item->date_from) && isset($item->personal_days))
                                                <td>{{ $item->personal_days }}</td>
                                            @else
                                                <td>N/A</td>
                                            @endif
                                        @endif
                                        @if (
                                            $category->category_name == 'Foreign - DA' ||
                                                $category->category_name == 'Local - DA (via Air)' ||
                                                $category->category_name == 'Local - DA')
                                            @if (isset($item->date_to) && isset($item->date_from) && isset($item->personal_days))
                                                <?php
                                                $from = new DateTime($item->date_from);
                                                $to = new DateTime($item->date_to);
                                                
                                                // Get full days difference
                                                $fullDays = $from->diff($to)->days;
                                                
                                                // Get remaining hours after subtracting full days
                                                $remainingHours = $from->diff($to)->h + $from->diff($to)->i / 60;
                                                
                                                // Initialize DA days count
                                                $da_days = 0;
                                                
                                                // Single-day case
                                                if ($fullDays == 0) {
                                                    if ($remainingHours >= 4 && $remainingHours < 8) {
                                                        $da_days = 0.5;
                                                    } elseif ($remainingHours >= 8) {
                                                        $da_days = 1;
                                                    }
                                                } else {
                                                    // Multi-day case
                                                    // Calculate first day's DA (hours from start to midnight)
                                                    $firstDayEnd = clone $from;
                                                    $firstDayEnd->modify('tomorrow')->setTime(0, 0);
                                                    $firstDayHours = $from->diff($firstDayEnd)->h + $from->diff($firstDayEnd)->i / 60;
                                                
                                                    if ($firstDayHours >= 4 && $firstDayHours < 8) {
                                                        $da_days += 0.5;
                                                    } elseif ($firstDayHours >= 8) {
                                                        $da_days += 1;
                                                    }
                                                
                                                    // Calculate last day's DA (hours from midnight to end time)
                                                    $lastDayStart = clone $to;
                                                    $lastDayStart->setTime(0, 0);
                                                    $lastDayHours = $to->diff($lastDayStart)->h + $to->diff($lastDayStart)->i / 60;
                                                
                                                    if ($lastDayHours >= 4 && $lastDayHours < 8) {
                                                        $da_days += 0.5;
                                                    } elseif ($lastDayHours >= 8) {
                                                        $da_days += 1;
                                                    }
                                                
                                                    // Add full middle days
                                                    if ($fullDays > 1) {
                                                        $da_days += $fullDays - 1;
                                                    }
                                                }
                                                
                                                // Subtract personal days
                                                $da_days -= $item->personal_days;
                                                ?>
                                                <td>{{ $da_days }}</td>
                                            @else
                                                <td>N/A</td>
                                                
                                            @endif
                                            <td>{{ $item->from }}</td>
                                            <td>{{ $item->to }}</td>
                                        @endif
                                        @if (isset($item->forex_amount))
                                            <td>{{ number_format($item->forex_amount, 0) }}</td>
                                        @else
                                            <td>{{ number_format($item->amount, 0) }}</td>
                                        @endif
                                        @if ($category->category_name == 'Medicines - Mother')
                                            @if ($item->amount_paid != 0)
                                                <td>{{ number_format($item->amount_paid, 0) }}</td>
                                            @else
                                                <td> - </td>
                                            @endif
                                        @endif
                                        @if (
                                            $category->category_name == 'Fuel - Mileage (within city)' ||
                                                $category->category_name == 'Fuel - Mileage (out of city)' ||
                                                $category->category_name == 'Fuel - Mileage')
                                            <td> {{ $item->kms }} </td>
                                            <td>{{ $item->from }}</td>
                                            <td>{{ $item->to }}</td>
                                        @endif
                                        @if (
                                            $category->category_name == 'Fuel - Receipts' ||
                                                $category->category_name == 'Fuel - Receipts' ||
                                                $category->category_name == 'Fuel - Receipts')
                                            <td> {{ $item->litres }} </td>
                                        @endif
                                        <td>{{ $item->receipt_copy == 1 ? 'Yes' : 'No' }}</td>
                                        <td>
                                            @if (isset($item->attachment) && $item->attachment != 'No File')
                                                <a href={{ url('claim-attachments/' . $item->attachment) }}
                                                    target="_blank">📂 View Attachment</a>
                                            @else
                                                ⛔ No File Found
                                            @endif

                                        </td>

                                        @if ($category->category_name == 'Medical - OPD')
                                            @if ($item->approved_amount != 0)
                                                <td>{{ number_format($item->approved_amount, 0) }}</td>
                                            @else
                                                @if (isset($voucher->currentApprover[0]) &&
                                                        (($voucher->currentApprover[0]->user_name == 'pl-medical-officer' &&
                                                            $employee->user_name == 'pl-medical-officer') ||
                                                            ($voucher->currentApprover[0]->user_name == 'pcl-medical-officer' &&
                                                                $employee->user_name == 'pcl-medical-officer') ||
                                                            ($voucher->currentApprover[0]->user_name == 'tpf-medical-officer' &&
                                                                $employee->user_name == 'tpf-medical-officer') ||
                                                            ($voucher->currentApprover[0]->user_name == 'dic-medical-officer' &&
                                                                $employee->user_name == 'dic-medical-officer') ||
                                                            ($voucher->currentApprover[0]->user_name == 'bsp-medical-officer' &&
                                                                $employee->user_name == 'bsp-medical-officer') ||
                                                            ($voucher->currentApprover[0]->user_name == 'omya-medical-officer' &&
                                                                $employee->user_name == 'omya-medical-officer') ||
                                                            ($voucher->currentApprover[0]->user_name == 'igi-medical-officer' &&
                                                                $employee->user_name == 'igi-medical-officer')))
                                                    <td> <input type="number" class="form-control"
                                                            name="approved_opd_amount[{{ $item->id }}]"
                                                            id="approved_opd_amount[{{ $item->id }}]"
                                                            value="<?= number_format($item->amount, 0, '.', '') ?>"></td>
                                                @else
                                                    <td>Approval Pending</td>
                                                @endif
                                            @endif
                                        @endif
                                        @if ($category->category_name == 'Local - DA (via Air)' || $category->category_name == 'Local - DA')
                                            <td> {{ $item->hotel_required == 1 ? 'Yes' : 'No' }} </td>
                                        @endif
                                        @if (count($voucher->voucherStatus) == 0)
                                            <td><a href="{{ URL::to('voucher/item/delete/' . $item->id) }}"
                                                    class="danger"><i class="la la-trash"></i></a></td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if (count($voucher->voucherStatus) == 0 && $voucher->status != 'Approved - Documents in transit')
                    @if ($voucher->categories($voucher->id)[0]->category_name == 'Medicines - Mother' && $voucher->employee->department->BusinessUnit->company->id == "1400")
                        <a
                            href="{{ URL::to($forwardingURL . '/' . 'medicines-mother-claim/submit' . '/' . $voucher->id) }}">
                            <button type="button" class="btn btn-success" style="padding: 5px 8px; font-size: 14px;">
                                <i class="la la-check-circle"></i> Send for Approval
                            </button>
                        </a>
                    @else
                        <a href="{{ URL::to($forwardingURL . '/' . 'first/approval' . '/' . $voucher->id) }}">
                            <button type="button" class="btn btn-success" style="padding: 5px 8px; font-size: 14px;">
                                <i class="la la-check-circle"></i> Send for Approval
                            </button>
                        </a>
                    @endif
                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#delete"
                        style="padding: 5px 8px; font-size: 14px;">
                        <i class="la la-times-circle"></i> Delete
                    </button>
                @else
                    @if (count($voucher->currentApprover) > 0)
                        @if ($voucher->currentApprover[0]->id == $employee->id)
                            @if ($voucher->currentApprover[0]->user_name == 'medicines-mother-admin')
                                <div class="row">
                                    <div class="col-10">
                                        <form method="post"
                                            action="{{ URL::to($forwardingURL . '/' . 'approve-medicines-mother' . '/' . $voucher->id) }}">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" class="form-control" name="approvedAmountHidden"
                                                id="approvedAmountHidden">
                                            <button type="submit" class="btn btn-sm btn-light-grey"><i
                                                    class="la la-check-circle "></i>
                                                Approve</button>
                                            <a class="reject" data-toggle="modal" data-target="#reject"><button
                                                    type="button" class="btn btn-sm btn-danger"><i
                                                        class="la la-times-circle"></i>
                                                    Reject</button></a>
                                        </form>
                                    </div>
                                    <div class="col-2 d-flex align-content-end w-25">
                                        @if (
                                            $voucher->currentApprover[0]->user_name == 'medicines-mother-admin' ||
                                                $voucher->currentApprover[0]->user_name == 'pl-medical-officer' ||
                                                $voucher->currentApprover[0]->user_name == 'tpf-medical-officer')
                                            <input type="number" class="form-control" name="approvedAmount"
                                                id="approvedAmount" placeholder="Approved Amount"
                                                oninput="setApprovedAmmount()">
                                        @endif
                                    </div>
                                </div>
                            @elseif(
                                $voucher->currentApprover[0]->user_name == 'pl-medical-officer' ||
                                    $voucher->currentApprover[0]->user_name == 'tpf-medical-officer')
                                <div class="col-10">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button type="submit" class="btn btn-sm btn-light-grey"><i
                                            class="la la-check-circle "></i>
                                        Approve</button>
                                    <a class="reject" data-toggle="modal" data-target="#reject"><button type="button"
                                            class="btn btn-sm btn-danger"><i class="la la-times-circle"></i>
                                            Reject</button></a>
                                    </form>
                                </div>
                            @else
                                <a href="{{ URL::to($forwardingURL . '/' . 'approve' . '/' . $voucher->id) }}"><button
                                        type="button" class="btn btn-sm btn-light-grey"><i
                                            class="la la-check-circle "></i>
                                        Approve</button></a>
                                <a class="reject" data-toggle="modal" data-target="#reject"><button type="button"
                                        class="btn btn-sm btn-danger"><i class="la la-times-circle"></i>
                                        Reject</button></a>
                            @endif

                        @endif
                    @endif
                    @if ($voucher->status == 'Rejected')
                        <form action="{{ URL::to('make/' . $forwardingURL . '/copy') }}" method="post"
                            id="voucher-form">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="text" name="voucher_id" value="{{ base64_encode($voucher->id * 5) }}"
                                hidden>
                        </form>
                    @endif
                    <hr>
                    @if ($voucher->status)
                        <button type="button" class="btn btn-sm btn-secondary dont-print"
                            onclick="javascript:printContent('print-panel');"><i class="la la-print"></i>
                            Print</button>
                        @if (view()->exists($customView))
                            @include($customView)
                        @endif
                    @endif
                @endif
            </div>
        </div>
        <div id="rightContentPanel" class="card col-12 col-lg-3 ml-5">
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
    <!-- Modal Popup -->
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
                <form method="post" action="{{ URL::to($forwardingURL . '/' . 'reject' . '/' . $voucher->id) }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="modal-body">
                        <h5>Are you sure you want to reject this {{ $panelHeading }} ?
                        </h5>
                        <hr>
                        <p>Rejecting this voucher will reject the previous approvals of
                            this
                            {{ $panelHeading }} as well
                        </p>
                        <label>Rejection Comments</label>
                        <textarea rows="5" class="form-control" name="rejection_comments" id="rejection_comments" maxlength="500"
                            required>{{ Input::old('rejection_comments') }}</textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-danger">Reject!</button>
                        <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
                    <a href="{{ URL::to('voucher/delete' . '/' . $voucher->id) }}"><button type="button"
                            class="btn btn-outline-danger">Confirm Delete</button></a>
                </div>
            </div>
        </div>
    </div>

    <div id="print-panel" hidden
        style="max-width: 800px; margin: 0 auto; border: 1px solid #eee; padding: 30px; box-shadow: 0 0 10px rgba(0,0,0,0.05); background-color: #fff;">
        <section style="border: none; box-shadow: none; margin: 0; padding: 0;">
            <div id="invoice-template" style="padding: 0;">

                <!-- Header Section: Logo Left, Text Centered -->
                <div style="position: relative; overflow: hidden; margin-bottom: 30px; min-height: 60px;">
                    <div style="float: left; padding-right: 15px; width: auto;">
                        @if ($company->logo)
                            <img src="{{ asset('assets/img' . '/' . $company->logo) }}" alt="company logo"
                                style="max-height: 60px; display: block;" />
                        @endif
                    </div>
                    <div style="text-align: center; margin: 0 70px;">
                        <h2 style="margin: 0; font-size: 24px; color: #333;">{{ $company->company_name }}</h2>
                        <p style="margin: 5px 0 0; font-size: 14px; color: #555;">Employee Reimbursement</p>
                    </div>
                    <div style="clear: both;"></div>

                    <div style="text-align: center; margin-top: 20px; margin-bottom: 20px;">
                        <h2 style="margin: 0; font-size: 32px; color: #333;">{{ $panelHeading }}</h2>
                        <h3 style="margin: 5px 0 0; font-size: 28px; color: #555;"># {{ $voucher->id }}</h3>
                    </div>
                </div>

                <div id="invoice-customer-details" style="margin-bottom: 30px; padding-top: 20px; text-align: center;">
                    <p style="font-weight: bold; font-size: 16px; margin: 0 0 10px; color: #666;">Employee Details</p>
                    <div>
                        <p style="margin: 0 0 5px; font-size: 15px;">
                            <strong>{{ $employee_sf_data->employee_name . ' - ' . $employee_sf_data->employee_number }}</strong>
                        </p>
                        <p style="margin: 0 0 5px; font-size: 15px;">{{ $employee_sf_data->designation }}</p>
                        <p style="margin: 0 0 5px; font-size: 15px;">
                            {{ $employee_sf_data->department()->first()->department_name . ' - ' . $employee_sf_data->businessUnit()->first()->bu_name }}
                        </p>
                        <p style="margin: 0 0 5px; font-size: 15px;">
                            {{ $employee->employeeGrade->primary_name . ' / ' . $employee_sf_data->cost_center }}</p>
                    </div>
                </div>

                <div style="margin-bottom: 30px; text-align: center;">
                    <p style="margin: 0 0 10px; font-size: 15px;"><span style="color: #666;">{{ $panelHeading }} Date
                            :</span> {{ date('d M, Y', strtotime($voucher->submission_date)) }}</p>
                    @if (isset($voucher->charge_to_cost_center) && $voucher->charge_to_cost_center != '')
                        <p style="margin: 0; font-size: 15px;"><span style="color: #666;">Charged to Cost Center :</span>
                            {{ $voucher->charge_to_cost_center }}</p>
                    @elseif(isset($voucher->order_number))
                        <p style="margin: 0; font-size: 15px;"><span style="color: #666;">Charged to Order #:</span>
                            {{ $voucher->order_number }}</p>
                    @endif
                </div>

                <div id="invoice-items-details" style="margin-bottom: 30px; padding-top: 20px;">
                    <div style="width: 100%; overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <th style="padding: 10px 0; text-align: left; font-size: 15px;">#</th>
                                    <th style="padding: 10px 0; text-align: left; font-size: 15px;">Description</th>
                                    <th style="padding: 10px 0; text-align: right; font-size: 15px;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($voucher->categories($voucher->id) as $category)
                                    <tr>
                                        <th scope="row" style="padding: 10px 0; vertical-align: top; font-size: 15px;">
                                            {{ $count++ }}</th>
                                        <td style="padding: 10px 0; vertical-align: top; font-size: 15px;">
                                            <p style="margin: 0;">{{ $category->category_name }}</p>
                                            @if ($category->category_name == 'Misc' || $category->category_name == 'Meal - Entertainment')
                                                @foreach ($voucher->voucherItems as $item)
                                                    @if ($item->category->category_name == $category->category_name)
                                                        @if (isset($item->description))
                                                            <p style="margin: 5px 0 0; font-size: 13px; color: #666;">
                                                                {!! $item->description !!}</p>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if ($category->category_name == 'Mobile - Card')
                                                @foreach ($voucher->voucherItems as $item)
                                                    @if ($item->category->category_name == $category->category_name)
                                                        @if (isset($item->month))
                                                            <p style="margin: 2px 0 0; font-size: 13px; color: #666;">For
                                                                the month of {!! $item->month !!}</p>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if ($voucher->receiptNotProvided($voucher->id, $category->id) > 0)
                                                <p
                                                    style="margin: 5px 0 0; font-size: 13px; color: #d9534f; font-weight: bold;">
                                                    Receipt not provided for 1 or more items of this category</p>
                                            @endif
                                        </td>
                                        <td
                                            style="padding: 10px 0; text-align: right; vertical-align: top; font-size: 15px;">
                                            <small
                                                style="font-size: 13px; color: #666;">{{ $employee_sf_data->company == 1 ? '(AED)' : '(PKR)' }}
                                            </small>{{ number_format($voucher->categoryTotalAmount($voucher->id, $category->id), 0) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="border-top: 1px solid #333; font-weight: bold;">
                                    <td colspan="2" style="padding: 10px 0; text-align: right; font-size: 16px;">Total
                                    </td>
                                    <td style="padding: 10px 0; text-align: right; font-size: 16px; font-weight: bold;">
                                        <small
                                            style="font-size: 14px; color: #666;">{{ $employee_sf_data->company == 1 ? '(AED)' : '(PKR)' }}
                                        </small>{{ number_format($voucher->totalAmount($voucher->id), 0) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div id="invoice-footer" style="margin-top: 30px; text-align: left;">
                    <div style="display: flex; flex-wrap: wrap; justify-content: flex-start;">
                        @foreach ($voucher->approvers as $approver)
                            <div style="width: 33.33%; padding: 0 15px; box-sizing: border-box; text-align: left;">
                                <p style="margin: 0 0 5px; font-size: 15px;">
                                    <strong>{{ $approver->employee_name }}</strong></p>
                                <p style="margin: 0 0 5px; font-size: 15px; color: #555;">{{ $approver->designation }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection
@section('footer')
    <script>
        function printContent(id) {
            var data = document.getElementById(id).innerHTML;
            var popupWindow = window.open('', 'printwin', 'left=100,top=100,width=1000,height=400');
            popupWindow.document.write('<HTML>\n<HEAD>\n');
            popupWindow.document.write('<TITLE>Print - {{ $panelHeading }} # {{ $voucher->id }}</TITLE>\n');
            popupWindow.document.write('<URL></URL>\n');

            popupWindow.document.write(
                "<style>html, body { min-font-size: 10px; color: #000; background-color: #ffffff; margin: 0; padding: 0; }</style>\n"
                );
            popupWindow.document.write("<style type='text/css' media='print'>@page { size: portrait; }</style>\n");
            popupWindow.document.write("<style type='text/css' media='print'>@page { margin: 10mm; }</style>\n");
            popupWindow.document.write('<script>\n');
            popupWindow.document.write('function print_win(){\n');
            popupWindow.document.write('    window.print();\n');
            popupWindow.document.write('    window.close();\n');
            popupWindow.document.write('}\n');
            popupWindow.document.write('<\/script>\n');
            popupWindow.document.write('</HEAD>\n');

            popupWindow.document.write('<BODY onload="print_win()">\n');
            popupWindow.document.write(data);
            popupWindow.document.write('</BODY>\n');
            popupWindow.document.write('</HTML>\n');
            popupWindow.document.close();
        }

        function print_win() {
            window.print();
            window.close();
        }
        $('#make-copy').on('click', function() {
            $('#voucher-form').submit();
        })

        function setApprovedAmmount() {
            var approvedAmount = $('#approvedAmount').val();
            $('#approvedAmountHidden').val(approvedAmount);
        }
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
