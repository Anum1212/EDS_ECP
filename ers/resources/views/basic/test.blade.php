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
                    <h4 class="card-title"> Details</h4>
                    <span>submitted by </span><br>
                    <span></span><br>
                    <div class="heading-elements">
                        <p class="badge badge-pill bg-danger"></p>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="project-time-line" data-voucher="">
                                <thead>
                                <tr>
                                    <th>Account #</th>
                                    <th>Mobile #</th>
                                    <th>Email</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-body">
                        <a href="#"><button type="button" class="btn btn-sm btn-success"><i class="la la-check-circle "></i> Approve</button></a>
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
                                    <form method="post" action="#}">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <div class="modal-body">
                                            <h5>Are you sure you want to reject this  ?</h5>
                                            <hr>
                                            <p>Rejecting this voucher will reject the previous approvals of this  as well</p>
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
                        <button type="button" class="btn btn-sm btn-secondary" onclick="javascript:printContent('print-panel');"><i class="la la-print"></i> Print</button>
                    </div>
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
                                    <h5>Are you sure you want to delete this voucher ?</h5>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                                    <a href="#"><button type="button" class="btn btn-outline-danger">Confirm Delete</button></a>
                                </div>
                            </div>
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
                            <h3 class="display-4 primary darken-1"></h3>
                            <div class="progress progress-sm mt-1 mb-0">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="category-title pb-1">
                        <h4>Approval Level(s)</h4>
                    </div>
                    <h6>Your  requires level <span class="badge badge-pill bg-danger"></span> approval</h6>
                    <hr>
                    <div class="category-title pb-1">
                        <h4> History</h4>
                    </div>
                    <div>
                        <small>Created on at </small>
                        <div class="progress progress-sm mt-0 mb-0">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small>Approved by </small>
                        <div class="progress progress-sm mt-0 mb-0">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
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
            popupWindow.document.write('<TITLE>Print - </TITLE>\n');
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