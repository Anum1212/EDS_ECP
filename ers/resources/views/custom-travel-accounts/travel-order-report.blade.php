@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/extensions/fixedHeader.dataTables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/custom/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/nouislider.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/ui/prism.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/extensions/noui-slider.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/colors/palette-noui.css')}}">
    <style>
        .table-employees tr td{
            padding: 5px;
        }
        .table-employees tr th{
            padding: 5px;
        }
    </style>
@endsection
@section('body')
    <div class="content-detached content-right">
        <div class="content-body">
            <section id="form-control-repeater">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="card-title white">Travel Order Report</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-right">
                            <a class="btn btn-xs white bg-success" style="padding: 5.5px" onclick="exportTableToExcel('table-employees', {{strtotime(date('Y-m-d H:i:s'))}})"><i class="la la-file-excel-o"></i> Excel</a>
                            <a class="btn btn-xs white bg-primary" style="padding: 5.5px" onclick="printContent('attendance-report')"><i class="la la-print"></i> Print</a>
                        </div>
                        <hr>
                                <div id="attendance-report" class="card-body card-dashboard w-100 overflow-auto">
                                <table class="full-width table-employees" id="table-employees" style="font-size: 7pt">
                                    <thead class="bg-dark white">
                                    <tr>
                                        <th style="width: 3%">Voucher ID</th>
                                        <th style="width: 5%">Submission Date</th>
                                        <th style="width: 3%">Status</th>
                                        <th style="width: 5%">Category</th>
                                        <th style="width: 5%">Date From</th>
                                        <th style="width: 5%">Date To</th>
                                        <th style="width: 2%">From</th>
                                        <th style="width: 2%">To</th>
                                        <th style="width: 2%">Advance Amount</th>
                                        <th style="width: 2%">Amount</th>
                                        <th style="width: 2%">Purpose</th>
                                        <th style="width: 2%">Category</th>
                                        <th style="width: 5%">Employee ID</th>
                                        <th style="width: 5%">Employee Name</th>
                                        <th style="width: 5%">Grade</th>
                                        <th style="width: 5%">Department</th>
                                        <th style="width: 5%">Business Unit</th>
                                        <th style="width: 5%">Company</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($voucher_item_details as $voucher_item_detail)
                                            <tr>
                                                <td class="border-bottom border-bottom-1">{{$voucher_item_detail->id}}</a></td>
                                                <td class="border-bottom border-bottom-1">{{$voucher_item_detail->submission_date}}</td>
                                                <td class="border-bottom border-bottom-1">{{$voucher_item_detail->status}}</td>
                                                <td class="border-bottom border-bottom-1">{{$voucher_item_detail->category_name}}</td>
                                                <td class="border-bottom border-bottom-1">{{date('d M, Y', strtotime($voucher_item_detail->date_from))}}</td>
                                                <td class="border-bottom border-bottom-1">{{date('d M, Y', strtotime($voucher_item_detail->date_to))}}</td>
                                                <td class="border-bottom border-bottom-1">{{$voucher_item_detail->from}}</td>
                                                <td class="border-bottom border-bottom-1">{{$voucher_item_detail->to}}</td>
                                                <td class="border-bottom border-bottom-1">{{$voucher_item_detail->advance_amount}}</td>
                                                <td class="border-bottom border-bottom-1">{{$voucher_item_detail->amount}}</td>
                                                <td class="border-bottom border-bottom-1">{{$voucher_item_detail->cur}}
                                                <td class="border-bottom border-bottom-1">{{$voucher_item_detail->purpose}}</td>
                                                <td class="border-bottom border-bottom-1">{{$voucher_item_detail->employee_number}}</td>
                                                <td class="border-bottom border-bottom-1">{{$voucher_item_detail->employee_name}}</td>
                                                <td class="border-bottom border-bottom-1">{{$voucher_item_detail->primary_name}}</td>
                                                <td class="border-bottom border-bottom-1">{{$voucher_item_detail->department_name}}</td>
                                                <td class="border-bottom border-bottom-1">{{$voucher_item_detail->bu_name}}</td>
                                                <td class="border-bottom border-bottom-1">{{$voucher_item_detail->company_name}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>
            </section>
        </div>
    </div>
    <div class="sidebar-detached sidebar-left">
        <div class="sidebar">
            <div class="sidebar-content card d-none d-lg-block">
                <form action="{{URL::to('travel-order/report')}}" method="post">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                @foreach($customFilters as $customFilter)
                                    @if(view()->exists($customFilter))
                                        @include($customFilter)
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button class="btn btn-primary" style="margin-top: 2.0rem"> Generate</button>
                        </div>
                    </div>
                </form>
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
            popupWindow.document.write('<TITLE>Print Travel Order Report</TITLE>\n');
            popupWindow.document.write('<URL></URL>\n');
            popupWindow.document.write("<link href='/app-assets/css/vendors.css' media='print' rel='stylesheet' type='text/css' />\n");
            popupWindow.document.write("<link href='/app-assets/css/app.css' media='print' rel='stylesheet' type='text/css' />\n");
            popupWindow.document.write("<link href='/assets/custom/style.css' media='print' rel='stylesheet' type='text/css' />\n");
            popupWindow.document.write("<link href='/app-assets/css/vendors.css' media='screen' rel='stylesheet' type='text/css' />\n");
            popupWindow.document.write("<link href='/app-assets/css/app.css' media='screen' rel='stylesheet' type='text/css' />\n");
            popupWindow.document.write("<link href='/assets/custom/style.css' media='screen' rel='stylesheet' type='text/css' />\n");
            popupWindow.document.write("<style>html body{min-font-size: 1px; color: #000;background-color: #ffffff}</style>\n");
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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/vendors/js/ui/jquery.sticky.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/ui/prism.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/jquery.raty.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/jquery.knob.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/wNumb.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/nouislider.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/js/scripts/extensions/knob.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/pages/content-sidebar.js')}}" type="text/javascript"></script>
    <script>
        $(".dataex-fixh-basic").DataTable({
            fixedHeader:{
                header:!0,
                headerOffset:$(".header-navbar").outerHeight()
            },
            "paging":false,
            "sorting":false,
            "searching":false,
            "info":false
        });
        if($("body").hasClass("vertical-layout"))
        {
            var menuWidth= $(".main-menu").outerWidth();
        }
    </script>
    <script>
        function exportTableToExcel(tableID, filename){
            var downloadLink;
            var dataType = 'application/vnd.ms-excel';
            var tableSelect = document.getElementById(tableID);
            var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

            // Specify file name
            filename = filename?filename+'.xls':'excel_data.xls';

            // Create download link element
            downloadLink = document.createElement("a");

            document.body.appendChild(downloadLink);

            if(navigator.msSaveOrOpenBlob){
                var blob = new Blob(['\ufeff', tableHTML], {
                    type: dataType
                });
                navigator.msSaveOrOpenBlob( blob, filename);
            }else{
                // Create a link to the file
                downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

                // Setting the file name
                downloadLink.download = filename;

                //triggering the function
                downloadLink.click();
            }
        }
    </script>
@endsection