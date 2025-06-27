@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/extensions/colReorder.dataTables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/extensions/buttons.dataTables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/extensions/fixedHeader.dataTables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/custom/vouchers.css')}}">
@endsection
@section('body')
    <div class="content-body">
        <section id="configuration">
            <div class="row">
                <div class="col-12">
                    <div class="card" style="overflow-x: scroll;">
                        <div class="card-header">
                                <h4 class="card-title">Bill Parking</h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a onclick="exportTableToExcel('bill-parking', 'Bill Parking')" data-table="bill-parking"><i class="la la-file-excel-o"></i>Export to Excel</a></li>
                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body card-dashboard w-100 overflow-auto">
                            <table class="table table-striped table-bordered text-inputs-searching file-export" id="bill-parking" data-name="Bank - Processed voucher as on {{date('d M, Y')}} ERS">
                                    <thead>
                                    <tr>
                                        <th>Voucher No.</th>
                                        <th>Employee No.</th>
                                        <th>Total Amount</th>
                                        <th>Line Item GL Account</th>
                                        <th>Line Item Cost Center</th>
                                        <th>Line Item Category</th>
                                        <th>Fuel Liters</th>
                                        <th>Vehicle Number</th>
                                        <th>Line Item Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($bankFormat as $voucher)
                                    @foreach($voucher['voucher_items'] as $item)
                                        <tr>
                                            <td style="mso-number-format: '\@'">{!! $voucher['voucher_number'] !!}</td>
                                            <td>{{$voucher['employee_number']}}</td>
                                            <td>{{$voucher['amount']}}</td>
                                            <td>{{$item->gl_code}}</td>
                                            <td>{{$voucher['cost_center']}}</td>
                                             <td>{{$item->category->category_name}}</td>
                                             @if(isset($item->litres))
                                             <td>{{$item->litres}}</td>
                                             @else 
                                             <td>NULL</td>
                                             @endif
                                               @if(isset($item->litres))
                                             <td>{{$item->vehicle_number}}</td>
                                             @else 
                                             <td>NULL</td>
                                             @endif
                                            <td>{{$item->amount}}</td>
                                            {{--*/ $totalAmount += $voucher['amount'] /*--}}
                                        </tr>
                                        @endforeach
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                    </div>
                    <div class="card" style="overflow-x: scroll;">
                        <div class="card-header">
                            <h4 class="card-title">Reimbursement Format</h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a onclick="exportTableToExcel('reimb-format', 'Reimbursement Format')" data-table="reimb-format"><i class="la la-file-excel-o"></i>Export to Excel</a></li>
                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body card-dashboard w-100 overflow-auto">
                            <table class="table table-striped table-bordered text-inputs-searching file-export" id="reimb-format" data-name="Reimbursement Format - Processed voucher as on {{date('d M, Y')}} ERS">
                                    <thead>
                                    <tr>
                                        <th>Employee No.</th>
                                        <th>Cost Center</th>
                                        <th>Voucher No.</th>
                                        <th>Total Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {{--*/ $counter = 1; /*--}}
                                    @foreach($bankFormat as $voucher)
                                        <tr>
                                            <td>{{$voucher['employee_number']}}</td>
                                            <td>{{$voucher['cost_center']}}</td>
                                            <td>{{$voucher['voucher_number']}}</td>
                                            <td>{{$voucher['amount']}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('footer')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/buttons.flash.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/jszip.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/pdfmake.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/vfs_fonts.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/buttons.html5.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/buttons.print.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/vendors/js/tables/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.colReorder.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-api.js')}}"></script>

    <script src="{{asset('assets/custom/jQuery.print.js')}}"></script>
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
        $('.table').dataTable({
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ]
        });
    </script>
@endsection