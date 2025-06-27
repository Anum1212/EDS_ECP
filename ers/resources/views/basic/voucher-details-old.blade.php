@extends(isset($layout)?'layouts.ers-layout':"layouts.base")
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
    <div class="content-detached content-right">
        <div class="content-body">
            <section id="description" class="card">
                <div class="card-header">
                    <h4 class="card-title">{{$panelHeading}} Details
                        @if(isset($voucher->child_id))
                            <strong>(Parent of <a href="{{URL::to($forwardingURL.'/'.'details'.'/'.$voucher->child_id)}}">{{$voucher->child_id}}</a>)</strong><br>
                        @endif
                        @if(isset($voucher->parent_id))
                            <strong>(Child of <a href="{{URL::to($forwardingURL.'/'.'details'.'/'.$voucher->parent_id)}}">{{$voucher->parent_id}}</a>)</strong><br>
                        @endif
                    </h4>
                    <span>submitted by {{$voucher->employee->employee_name}}</span><br>
                    <span>{{$voucher->employee->employee_number}}</span><br>
                    <div class="heading-elements">
                        @if($voucher->status == 'Submitted')
                            <p class="badge badge-pill bg-primary">{{$voucher->status}}</p>
                        @elseif($voucher->status == 'Approved - Documents in Transit')
                            <p class="badge badge-pill bg-warning">{{$voucher->status}}</p>
                        @elseif($voucher->status == 'In Process')
                            <p class="badge badge-pill bg-yellow">{{$voucher->status}}</p>
                        @elseif($voucher->status == 'Processed')
                            <p class="badge badge-pill bg-success">{{$voucher->status}}</p>
                        @elseif($voucher->status == 'Rejected')
                            <p class="badge badge-pill bg-danger">{{$voucher->status}}</p>
                        @endif
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="table-responsive">
                            @if($voucher->status == 'Rejected')
                                @foreach($voucher->approvers as $approver)
                                    @if(isset($approver->pivot->comments))
                                        <div class="bg-danger p-1 text-center">
                                            <h2 class="white">{{$approver->pivot->comments}}</h2>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                            @if($voucher->status == 'Posted')
                                <div class="bg-gradient-x-grey-blue bg-darken-4 p-1 text-center">
                                    <h2 class="white">Posted</h2>
                                    <h4 class="white">Thank you for using SAP Global Benefits</h4>
                                </div>
                            @endif
                            <table class="table" id="project-time-line" data-voucher="{{$voucher->id}}">
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
                                        {{-- @foreach($voucher->employee->bankAccounts as $bankAccount)
                                            {{$bankAccount->bank_name.' - '.$bankAccount->pivot->account_number}}
                                        @endforeach --}}
                                        {{$employee_sf_data->account_number}}
                                    </td>
                                    <td>{{$employee_sf_data->mobile}}</td>
                                    <td>{{$employee_sf_data->email}}</td>
                                </tr>
                                </tbody>
                            </table>
                            {{-- @if($voucher->department_id && $voucher->department_id != '') --}}
                            @if($voucher->charge_to_cost_center)
                                @if(count($voucher->voucherStatus)>0)
                                <h6 class="display-5 danger darken-1 mt-1 mb-0">Charged to Cost Center <strong>{{$voucher->charge_to_cost_center}}</strong></h6>
                                @else
                                <h6 class="display-5 danger darken-1 mt-1 mb-0">Charge to Cost Center <strong>{{$voucher->charge_to_cost_center}}</strong></h6>
                                @endif
                            @elseif(isset($voucher->order_number))
                                @if(count($voucher->voucherStatus)>0)
                                <h6 class="display-5 danger darken-1 mt-1 mb-0">Charged to Order # <strong>{{$voucher->order_number}}</strong></h6>
                                @else
                                    <h6 class="display-5 danger darken-1 mt-1 mb-0">Charge to Order # <strong>{{$voucher->order_number}}</strong></h6>
                                @endif
                            @endif
                        </div>
                    </div>
                    @if(count($voucher->voucherStatus) == 0)
                    <div class="card-body">
                        <a href="{{URL::to($forwardingURL.'/'.'first/approval'.'/'.$voucher->id)}}"><button type="button" class="btn btn-sm btn-success"><i class="la la-send"></i> Send for Approval</button></a>
                        <a href="{{URL::to($forwardingURL.'/'.'add'.'/'.$voucher->id)}}"><button type="button" class="btn btn-sm btn-primary"><i class="la la-pencil"></i> Add More Expenses</button></a>
                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete"><i class="la la-trash-o"></i> Delete</button>
                    </div>
                    @else
                    <div class="card-body">
                        @if(count($voucher->currentApprover) > 0)
                            @if($voucher->currentApprover[0]->id == $employee->id)
                                <a href="{{URL::to($forwardingURL.'/'.'approve'.'/'.$voucher->id)}}"><button type="button" class="btn btn-sm btn-success"><i class="la la-check-circle "></i> Approve</button></a>
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
                                            <form method="post" action="{{URL::to($forwardingURL.'/'.'reject'.'/'.$voucher->id)}}">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <div class="modal-body">
                                                    <h5>Are you sure you want to reject this {{$panelHeading}} ?</h5>
                                                    <hr>
                                                    <p>Rejecting this voucher will reject the previous approvals of this {{$panelHeading}} as well</p>
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
                        @if($voucher->status == 'Rejected')
                            <form action="{{URL::to('make/'.$forwardingURL.'/copy')}}" method="post" id="voucher-form">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="text" name="voucher_id" value="{{base64_encode($voucher->id*5)}}" hidden>
                            </form>
                        @endif
                        <button type="button" class="btn btn-sm btn-primary" id="make-copy"><i class="la la-copy"></i> Make a copy</button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="javascript:printContent('print-panel');"><i class="la la-print"></i> Print</button>
                        <hr>
                        @if($voucher->status == 'Approved - Documents in transit')
                            @if(view()->exists($customView))
                                @include($customView)
                            @endif
                        @endif
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
                                    <h5>Are you sure you want to delete this {{$panelHeading}} ?</h5>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                                    <a href="{{URL::to('voucher/delete'.'/'.$voucher->id)}}"><button type="button" class="btn btn-outline-danger">Confirm Delete</button></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @foreach($voucher->categories($voucher->id) as $category)
                @include('category-tables.'.$type.'.'.$category->view)
            @endforeach
        </div>
        <div class="content-body" id="print-panel" hidden>
            <section class="card">
                <div id="invoice-template" class="card-body">
                    <div id="invoice-company-details" class="row">
                        <div class="col-md-6 col-sm-12 text-center text-md-left">
                            <div class="media">
                                {{-- @if($voucher->department->businessUnit->company->logo) --}}
                                    <img src="{{asset('assets/img'.'/'.'logo-packages.png')}}" alt="company logo" class=""/>
                                {{-- @endif --}}
                                <div class="media-body">
                                    <ul class="ml-2 px-0 list-unstyled">
                                        {{-- <h2>{{$voucher->department->businessUnit->company->company_name}}</h2> --}}
                                        <li class="text-bold-800">Employee Reimbursement</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 text-center text-md-right">
                            <h2>{{$panelHeading}}</h2>
                            <h3># {{$voucher->id}}</h3>
                        </div>
                    </div>
                    <div id="invoice-customer-details" class="row pt-2">
                        <div class="col-sm-12 text-center text-md-left">
                            <p class="text-muted">Employee Details</p>
                        </div>
                        <div class="col-md-6 col-sm-12 text-center text-md-left">
                            <ul class="px-0 list-unstyled">
                                <li class="text-bold-800"><strong>{{$voucher->employee->employee_name.' - '.$voucher->employee->employee_number}}</strong></li>
                                <li>{{$voucher->employee->designation}}</li>
                                <li>{{$voucher->employee->department->department_name.' - '.$voucher->employee->department->businessUnit->bu_name}}</li>
                                <li>{{$voucher->employee->employeeGrade->primary_name.' / '.$voucher->employee->department->cost_center}}</li>
                            </ul>
                        </div>
                        <div class="col-md-6 col-sm-12 text-center text-md-right">
                            <p><span class="text-muted">{{$panelHeading}} Date :</span> {{date('d M, Y',strtotime($voucher->submission_date))}}</p>
                            @if(isset($voucher->charge_to_cost_center) && $voucher->charge_to_cost_center != '')
                                <p><span class="text-muted">Charged to Cost Center :</span> {{$voucher->charge_to_cost_center}}</p>
                            @elseif(isset($voucher->order_number))
                                <p><span class="text-muted">Charged to Order #:</span> {{$voucher->order_number}}</p>
                            @endif
                        </div>
                    </div>
                    <div id="invoice-items-details" class="pt-2">
                        <div class="row">
                            <div class="table-responsive col-sm-12">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Description</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($voucher->categories($voucher->id) as $category)
                                        <tr>
                                            <th scope="row">{{$count++}}</th>
                                            <td>
                                                <p style="margin-bottom: 0px">{{$category->category_name}}</p>
                                                @if($category->category_name == 'Misc' || $category->category_name == 'Meal - Entertainment')
                                                    @foreach($voucher->voucherItems as $item)
                                                        @if($item->category->category_name == $category->category_name)
                                                            @if(isset($item->description))
                                                                <small>{!! $item->description !!}</small> <br>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @endif
                                                @if($category->category_name == 'Mobile - Card')
                                                    @foreach($voucher->voucherItems as $item)
                                                        @if($item->category->category_name == $category->category_name)
                                                            @if(isset($item->month))
                                                                <small>For the month of {!! $item->month !!}</small> <br>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @endif
                                                @if($voucher->receiptNotProvided($voucher->id, $category->id) > 0)
                                                    <small><strong>Receipt not provided for 1 or more items of this category</strong></small>
                                                @endif
                                            </td>
                                            <td class="text-right"><small>PKR </small>{{number_format($voucher->categoryTotalAmount($voucher->id, $category->id), 0)}}</td>
                                        </tr>
                                    @endforeach
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
                                            <td class="text-bold-800">Total</td>
                                            <td class="text-bold-800 text-right">PKR {{number_format($voucher->totalAmount($voucher->id), 0)}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="invoice-footer">
                        <div class="row">
                            @foreach($voucher->voucherStatus as $status)
                                <div class="col-md-4">
                                    <ul class="list-unstyled">
                                        <li><strong>{{$status->employee_name}}</strong></li>
                                        <li class="text-muted">{{$status->designation}}</li>
                                    </ul>
                                </div>
                            @endforeach
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
                            <h3 class="display-5 primary darken-1">Total Amount (PKR)</h3>
                            <h3 class="display-4 primary darken-1">{{number_format($voucher->totalAmount($voucher->id), 0)}}</h3>
                            @if($voucher->is_travel_order == 1 && $voucher->totalAmount($voucher->id) == 0 && $voucher->totalAmountForex($voucher->id) > 0)
                                <small>PKR amount will be calculated after the confirmation of Forex Rate by <br><strong>Accounts Department</strong></small>
                            @endif
                            @if($voucher->advanceTotalAmount($voucher->id))
                                <span>Advance Amount<br><h3>PKR {{number_format($voucher->advanceTotalAmount($voucher->id),0)}}</h3></span>
                            @endif
                            <div class="progress progress-sm mt-1 mb-0">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="category-title pb-1">
                        <h4>Approval Level(s)</h4>
                    </div>
                    <h6>Your {{$panelHeading}} requires level <span class="badge badge-pill bg-danger">{{count($voucher->requiresSecondApproval())==0?1:2}}</span> approval</h6>
                    <hr>
                    <div class="category-title pb-1">
                        <h4>{{$panelHeading}} History</h4>
                    </div>
                    <div>
                        <small>Created on {{date('M d,Y', strtotime($voucher->created_at))}} at {{date('H:i:s', strtotime($voucher->created_at))}}</small>
                        <div class="progress progress-sm mt-0 mb-0">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        @foreach($voucher->voucherStatus as $status)
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
                        @if($voucher->status == 'Approved - Documents in transit')
                            <small>Documents in Transit</small>
                            <div class="progress progress-sm mt-0 mb-0">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        @endif
                        @if($voucher->status == 'In Process')
                            <small>In Process</small>
                            <div class="progress progress-sm mt-0 mb-0">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        @endif
                        @if(isset($voucher->processed_at))
                            <small>Processed on {{date('M d,Y', strtotime($voucher->processed_at))}} at {{date('H:i:s', strtotime($voucher->processed_at))}}</small>
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
            popupWindow.document.write('<TITLE>Print - {{$panelHeading}} # {{$voucher->id}}</TITLE>\n');
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