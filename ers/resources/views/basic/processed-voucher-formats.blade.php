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
            <div class="row justify-content-center">
                <div class="col-10">
                    <div class="card" style="overflow-x: scroll;">
                        <div class="card-header">
                            <h4 class="card-title">Bank Format</h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a onclick="exportTableToExcel('bank-format', 'Bank Format')" data-table="bank-format"><i class="la la-file-excel-o"></i>Export to Excel</a></li>
                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body card-dashboard w-100 overflow-auto">
                            <table class="table table-striped table-bordered text-inputs-searching file-export" id="bank-format" data-name="Bank - Processed voucher as on {{date('d M, Y')}} ERS">
                                    <thead>
                                    <tr>
                                        <th>Batch No.</th>
                                        <th></th>
                                        <th>Account No.</th>
                                        <th></th>
                                        <th>Branch Code</th>
                                        <th></th>
                                        <th>Date</th>
                                        <th></th>
                                        <th>Trcx. Type</th>
                                        <th></th>
                                        <th>Amount</th>
                                        <th></th>
                                        <th>Serial No.</th>
                                        <th>Data Delimiter</th>
                                        <th>Narration</th>
                                        <th>API Status</th>
                                        <th>API Response</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($bankFormat as $voucher)
                                        <tr>
                                            <td></td>
                                            <td>;</td>
                                            <td style="mso-number-format: '\@'">{!! $voucher['account_number'] !!}</td>
                                            <td>;</td>
                                            <td>8034</td>
                                            <td>;</td>
                                            <td>{{date('dmY')}}</td>
                                            <td>;</td>
                                            <td>THCR</td>
                                            <td>;</td>
                                            <td>{{$voucher['amount']}}</td>
                                            <td>;</td>
                                            <td>{{$counter++}}</td>
                                            <td>;;;;</td>
                                            <td>{{$voucher['employee_name']}}</td>
                                            <td>{{ isset($voucher['api_status_icon']) ? $voucher['api_status_icon'] : '' }}</td>
                                            <td>{{ isset($voucher['api_response']) ? $voucher['api_response'] : '' }}</td>
                                            {{--*/ $totalAmount += $voucher['amount'] /*--}}
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td><strong>Total</strong></td>
                                        <td>;</td>
                                        <td></td>
                                        <td>;</td>
                                        <td></td>
                                        <td>;</td>
                                        <td></td>
                                        <td>;</td>
                                        <td></td>
                                        <td>;</td>
                                        <td><strong>{{$totalAmount}}</strong></td>
                                        <td>;</td>
                                        <td></td>
                                        <td>;;;;</td>
                                        <td></td>
                                    </tr>
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
                        <div class="card-content collapse show">
                            <div class="card-body card-dashboard" id="reimb-format-export">
                                <table class="table table-striped table-bordered text-inputs-searching file-export" id="reimb-format" data-name="Reimbursement Format - Processed voucher as on {{date('d M, Y')}} ERS">
                                    <thead>
                                    <tr>
                                        <th>S NO</th>
                                        <th>Dr Account</th>
                                        <th>Employer Code</th>
                                        <th>Dr Amount</th>
                                        <th>Dr Entity</th>
                                        <th>Payment Date</th>
                                        <th>EMP NO</th>
                                        <th>Cr Account Title</th>
                                        <th>Cr Account No</th>
                                        <th>Cr Amount</th>
                                        <th>Cr CNIC</th>
                                        <th>Cr Entity</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {{--*/ $counter = 1; /*--}}
                                    <tr>
                                        <td></td>
                                        <td><strong>2566777</strong></td>
                                        <td><strong>A014</strong></td>
                                        <td>{{number_format($totalAmount,0)}}</td>
                                        <td>MCB</td>
                                        <td>{{date('d-M-y')}}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    @foreach($bankFormat as $voucher)
                                        <tr>
                                            <td>{{$counter++}}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{$voucher['employee_number']}}</td>
                                            <td>{{$voucher['employee_name']}}</td>
                                            <td style="mso-number-format: '\@'">{!! $voucher['account_number'] !!}</td>
                                            <td>{{$voucher['amount']}}</td>
                                            <td>{{$voucher['cnic']}}</td>
                                            <td>MCB</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{number_format($totalAmount,0)}}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
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