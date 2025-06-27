@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/animate/animate.css')}}">
@endsection
@section('body')
    <div class="content-detached content-right">
        <div class="content-body">
            <section id="description" class="card">
                <div class="card-header">
                    <h4 class="card-title">Travel Order Details</h4>
                    <span>submitted by {{$travelOrder->employee->employee_name}}</span><br>
                    <span>{{$travelOrder->employee->employee_number}}</span>
                    <div class="heading-elements">
                        @if($travelOrder->status == 'Submitted')
                            <p class="badge badge-pill bg-primary">{{$travelOrder->status}}</p>
                        @elseif($travelOrder->status == 'Approved')
                            <p class="badge badge-pill bg-warning">{{$travelOrder->status}}</p>
                        @elseif($travelOrder->status == 'In Process')
                            <p class="badge badge-pill bg-yellow">{{$travelOrder->status}}</p>
                        @elseif($travelOrder->status == 'Processed')
                            <p class="badge badge-pill bg-success">{{$travelOrder->status}}</p>
                        @elseif($travelOrder->status == 'Rejected')
                            <p class="badge badge-pill bg-danger">{{$travelOrder->status}}</p>
                        @endif
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="table-responsive">
                            @if($travelOrder->status == 'Rejected')
                                @foreach($travelOrder->approvers as $approver)
                                    @if(isset($approver->pivot->comments))
                                        <div class="bg-danger p-1 text-center">
                                            <h2 class="white">{{$approver->pivot->comments}}</h2>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                            @if($travelOrder->status == 'Posted')
                                <div class="bg-gradient-x-grey-blue bg-darken-4 p-1 text-center">
                                    <h2 class="white">Posted</h2>
                                    <h4 class="white">Thank you for using ERS</h4>
                                </div>
                            @endif
                            <table class="table" id="project-time-line" data-voucher="{{$travelOrder->id}}">
                                <thead>
                                <tr>
                                    <th>Account #</th>
                                    <th>Mobile #</th>
                                    <th>Email</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        @foreach($travelOrder->employee->bankAccounts as $bankAccount)
                                            {{$bankAccount->bank_name.' - '.$bankAccount->pivot->account_number}}
                                        @endforeach
                                    </td>
                                    <td>{{$travelOrder->employee->mobile}}</td>
                                    <td>{{$travelOrder->employee->email}}</td>
                                </tr>
                                </tbody>
                            </table>
                            @if(isset($travelOrder->charge_to_cost_center) && $travelOrder->charge_to_cost_center != '')
                                @if(count($travelOrder->voucherStatus)>0)
                                <h6 class="display-5 danger darken-1 mt-1 mb-0">Charged to Cost Center <strong>{{$travelOrder->charge_to_cost_center}}</strong></h6>
                                @else
                                <h6 class="display-5 danger darken-1 mt-1 mb-0">Charge to Cost Center <strong>{{$travelOrder->charge_to_cost_center}}</strong></h6>
                                @endif
                            @elseif(isset($travelOrder->order_number))
                                @if(count($travelOrder->voucherStatus)>0)
                                <h6 class="display-5 danger darken-1 mt-1 mb-0">Charged to Order # <strong>{{$travelOrder->order_number}}</strong></h6>
                                @else
                                    <h6 class="display-5 danger darken-1 mt-1 mb-0">Charge to Order # <strong>{{$travelOrder->order_number}}</strong></h6>
                                @endif
                            @endif
                        </div>
                    </div>
                    @if(count($travelOrder->travelOrderStatus) == 0)
                    <div class="card-body">
                        <a href="{{URL::to('travel-order/send/approval'.'/'.$travelOrder->id)}}"><button type="button" class="btn btn-sm btn-success"><i class="la la-send"></i> Send for Approval</button></a>
                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete"><i class="la la-trash-o"></i> Delete</button>
                    </div>
                    @else
                    <div class="card-body">
                        @if(count($travelOrder->currentApprover) > 0)
                            @if($travelOrder->currentApprover[0]->id == $employee->id)
                                <a href="{{URL::to('travel-order/approve'.'/'.$travelOrder->id)}}"><button type="button" class="btn btn-sm btn-success"><i class="la la-check-circle "></i> Approve</button></a>
                                <a class="reject" data-toggle="modal" data-target="#reject"><button type="button" class="btn btn-sm btn-danger"><i class="la la-times-circle"></i> Reject</button></a>
                                <div class="modal animated shake text-left" id="reject" tabindex="-1" role="dialog" aria-labelledby="myModalLabel10" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger white">
                                                <h4 class="modal-title white" id="myModalLabel10">Reject</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form method="post" action="{{URL::to('travel-order/reject'.'/'.$travelOrder->id)}}">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <div class="modal-body">
                                                    <h5>Are you sure you want to reject this travel order ?</h5>
                                                    <hr>
                                                    <p>Rejecting this travel order will reject the previous approvals of this travel order as well</p>
                                                    <label>Rejection Comments</label>
                                                    <textarea rows="5" class="form-control" name="rejection_comments" id="rejection_comments" maxlength="500" required>{{Input::old('rejection_comments')}}</textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-outline-danger">Reject!</button>
                                                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                        <button type="button" class="btn btn-sm btn-secondary" onclick="javascript:printContent('print-panel');"><i class="la la-print"></i> Print</button>
                    </div>
                    @endif
                    <div class="modal animated shake text-left" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel10" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger white">
                                    <h4 class="modal-title white" id="myModalLabel10">Delete</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <h5>Are you sure you want to delete this travel order ?</h5>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                                    <a href="{{URL::to('travel-order/delete'.'/'.$travelOrder->id)}}"><button type="button" class="btn btn-outline-danger">Confirm Delete</button></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <div class="card">
                <div class="card-header card-head-inverse bg-primary">
                    <a data-action="collapse"><h4 class="card-title text-white">Travel Order Details <span class="badge badge bg-white text-secondary badge-pill mr-2"></span></h4></a>
                </div>
                <div class="card-content" style="overflow-x: auto">
                    <div class="card-body">
                        <table class="table table-white-space table-bordered display no-wrap table-middle">
                            <thead>
                            <tr>
                                <th>Travel Order Type</th>
                                <th>Date From</th>
                                <th>Date To</th>
                                <th>Purpose</th>
                                <th>Places to visit</th>
                                <th>Accomodation</th>
                                <th>Forex Amount</th>
                                <th>Bond</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr class="taskrow">
                                    <td>{{$travelOrder->travel_order_type}}</td>
                                    <td>{{date('d M, Y H:i:s', strtotime($travelOrder->date_from))}}</td>
                                    <td>{{date('d M, Y H:i:s', strtotime($travelOrder->date_to))}}</td>
                                    <td>{{$travelOrder->purpose}}</td>
                                    <td>{{$travelOrder->places_to_visit}}</td>
                                    <td>{{$travelOrder->accomodation}}</td>
                                    <td>{{$travelOrder->forex_amount}}</td>
                                    <td>
                                        @if($travelOrder->travel_order_type == 'Local')
                                            <span class="badge badge-pill bg-primary">N/A</span>
                                        @elseif($travelOrder->travel_order_type == 'Foreign')
                                            <a href="{{URL::to('travel-bonds'.'/'.$travelOrder->bond)}}" target="_blank"><span class="badge badge-pill bg-success">Click Here</span></a>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body" id="print-panel" hidden>
            <section class="card">
                <div id="invoice-template" class="card-body">
                    <div id="invoice-company-details" class="row">
                        <div class="col-md-6 col-sm-12 text-center text-md-left">
                            <div class="media">
                                <img src="{{asset('assets/img/logo-packages.png')}}" alt="company logo" class=""/>
                                <div class="media-body">
                                    <ul class="ml-2 px-0 list-unstyled">
                                        <h2>Packages Limited</h2>
                                        <li class="text-bold-800">Employee Reimbursement</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 text-center text-md-right">
                            <h2>TRAVEL ORDER</h2>
                            <h3># {{$travelOrder->id}}</h3>
                        </div>
                    </div>
                    <div id="invoice-customer-details" class="row pt-2">
                        <div class="col-sm-12 text-center text-md-left">
                            <p class="text-muted">Employee Details</p>
                        </div>
                        <div class="col-md-6 col-sm-12 text-center text-md-left">
                            <ul class="px-0 list-unstyled">
                                <li class="text-bold-800"><strong>{{$travelOrder->employee->employee_name.' - '.$travelOrder->employee->employee_number}}</strong></li>
                                <li>{{$travelOrder->employee->designation}}</li>
                                <li>{{$travelOrder->employee->department->department_name.' - '.$travelOrder->employee->department->businessUnit->bu_name}}</li>
                                <li>{{$travelOrder->employee->employeeGrade->primary_name.' / '.$travelOrder->employee->department->cost_center}}</li>
                            </ul>
                        </div>
                        <div class="col-md-6 col-sm-12 text-center text-md-right">
                            <p><span class="text-muted">Travel Order Date :</span> {{date('d M, Y',strtotime($travelOrder->created_at))}}</p>
                            @if(isset($travelOrder->charge_to_cost_center) && $travelOrder->charge_to_cost_center != '')
                                <p><span class="text-muted">Charged to Cost Center :</span> {{$travelOrder->charge_to_cost_center}}</p>
                            @elseif(isset($travelOrder->order_number))
                                <p><span class="text-muted">Charged to Order #:</span> {{$travelOrder->order_number}}</p>
                            @endif
                        </div>
                    </div>
                    <div id="invoice-items-details" class="pt-2">
                        <div class="row">
                            <div class="table-responsive col-sm-12">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>
                                                <p style="margin-bottom: 0px">Travel Order Type</p>
                                            </th>
                                            <td class="text-right">{{$travelOrder->travel_order_type}}</td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <p style="margin-bottom: 0px">Duration</p>
                                            </th>
                                            <td class="text-right">{{date('d M, Y H:i:s', strtotime($travelOrder->date_from)).' to '.date('d M, Y H:i:s', strtotime($travelOrder->date_to))}}</td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <p style="margin-bottom: 0px">Purpose</p>
                                            </th>
                                            <td class="text-right">{{$travelOrder->purpose}}</td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <p style="margin-bottom: 0px">Places to visit</p>
                                            </th>
                                            <td class="text-right">{{$travelOrder->places_to_visit}}</td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <p style="margin-bottom: 0px">Accomodation</p>
                                            </th>
                                            <td class="text-right">{{$travelOrder->accomodation}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-7 col-sm-12 text-center text-md-left">
                                <div class="row">
                                    <div class="col-md-8">

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <td class="text-bold-800">Total Advance Amount</td>
                                            <td class="text-bold-800 text-right">PKR {{number_format($travelOrder->amount, 0)}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="invoice-footer">
                        <div class="row">

                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div class="sidebar-detached sidebar-left" ,=",">
        <div class="sidebar">
            <div class="sidebar-content card d-none d-lg-block">
                <div class="card-body">
                    <div class="card-body text-center p-2">
                        <div class="card-header mb-0">
                            <h3 class="display-5 primary darken-1">Advance Amount (PKR)</h3>
                            <h3 class="display-4 primary darken-1">{{number_format($travelOrder->amount, 0)}}</h3>
                            <div class="progress progress-sm mt-1 mb-0">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="category-title pb-1">
                        <h4>Travel Order History</h4>
                    </div>
                    <div>
                        <small>Created on {{date('M d,Y', strtotime($travelOrder->created_at))}} at {{date('H:i:s', strtotime($travelOrder->created_at))}}</small>
                        <div class="progress progress-sm mt-0 mb-0">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        @foreach($travelOrder->travelOrderStatus as $status)
                            @if(isset($status->pivot->approved))
                                @if($status->pivot->approved == 1)
                                    <small>Approved by {{$status->employee_name == $employee->employee_name?'Self':$status->employee_name}} on {{date('M d,Y', strtotime($status->pivot->updated_at))}} at {{date('H:i:s', strtotime($status->pivot->updated_at))}}</small>
                                    <div class="progress progress-sm mt-0 mb-0">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                @elseif($status->pivot->approved == 0)
                                    <small>Rejected by {{$status->employee_name}} on {{date('M d,Y', strtotime($status->pivot->updated_at))}} at {{date('H:i:s', strtotime($status->pivot->updated_at))}}</small>
                                    <div class="progress progress-sm mt-0 mb-0">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                @endif
                            @else
                                <small>Sent to {{$status->employee_name}} for approval on {{date('M d,Y', strtotime($status->pivot->created_at))}} at {{date('H:i:s', strtotime($status->pivot->created_at))}}</small>
                                <div class="progress progress-sm mt-0 mb-0">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            @endif
                        @endforeach
                        @if($travelOrder->status == 'Approved - Documents in transit')
                            <small>Documents in Transit</small>
                            <div class="progress progress-sm mt-0 mb-0">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        @endif
                        @if($travelOrder->status == 'In Process')
                            <small>In Process</small>
                            <div class="progress progress-sm mt-0 mb-0">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        @endif
                        @if(isset($travelOrder->processed_at))
                            <small>Processed on {{date('M d,Y', strtotime($travelOrder->processed_at))}} at {{date('H:i:s', strtotime($travelOrder->processed_at))}}</small>
                            <div class="progress progress-sm mt-0 mb-0">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        @endif
                    </div>
                    <hr>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer')
    <script>
        function printContent(id){
            var data = document.getElementById(id).innerHTML;
            var popupWindow = window.open('','printwin', 'left=100,top=100,width=1000,height=400');
            popupWindow.document.write('<HTML>\n<HEAD>\n');
            popupWindow.document.write('<TITLE>Print - Voucher # {{$travelOrder->id}}</TITLE>\n');
            popupWindow.document.write('<URL></URL>\n');
            popupWindow.document.write("<link href='/app-assets/css/vendors.css' media='print' rel='stylesheet' type='text/css' />\n");
            popupWindow.document.write("<link href='/app-assets/css/app.css' media='print' rel='stylesheet' type='text/css' />\n");
            popupWindow.document.write("<link href='/app-assets/css/vendors.css' media='screen' rel='stylesheet' type='text/css' />\n");
            popupWindow.document.write("<link href='/app-assets/css/app.css' media='screen' rel='stylesheet' type='text/css' />\n");
            popupWindow.document.write("<style>html body{min-font-size: 10px; color: #000;background-color: #ffffff}</style>\n");
            popupWindow.document.write("<style type=text/css media=print>@page { size: portrait; }</style>\n");
            popupWindow.document.write("<style type=text/css media=print>@page { margin: 10; }</style>\n");
            popupWindow.document.write('<script>\n');
            popupWindow.document.write('function print_win(){\n');
            popupWindow.document.write('\nwindow.print();\n');
            popupWindow.document.write('\nwindow.close();\n');
            popupWindow.document.write('}\n');
            popupWindow.document.write('<\/script>\n');
            popupWindow.document.write('</HEAD>\n');
            popupWindow.document.write('<BODY onload="print_win()">\n');
            popupWindow.document.write(data);
            popupWindow.document.write('</BODY>\n');
            popupWindow.document.write('</HTML>\n');
            popupWindow.document.close();
        }
        function print_win(){
            window.print();
            window.close();
        }
    </script>
    <script>
        $('#make-copy').on('click', function(){
            $('#voucher-form').submit();
        })
    </script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/modal/components-modal.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/pages/custom-project-details/project-task-list.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/jquery.knob.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/cards/card-statistics.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/export-word/js/FileSaver.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/export-word/js/jquery.wordexport.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/charts/echarts/echarts.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/charts/echarts/pie-doughnut/doughnut-infographic.js')}}" type="text/javascript"></script>
@endsection