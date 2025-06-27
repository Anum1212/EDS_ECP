@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/extensions/colReorder.dataTables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/extensions/buttons.dataTables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/extensions/fixedHeader.dataTables.min.css')}}">
    <style>
        .textFormat{
            mso-number-format: "\@";
        }
    </style>
@endsection
@section('body')
    <div class="content-body">
        <section id="configuration">
            <div class="row">
                <div class="col-12">
                    <div class="card" style="overflow-x: scroll;">
                        <div class="card-header">
                            <h4 class="card-title">Stats Report</h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a onclick="exportTableToExcel()" data-table="ers-stats"><i class="la la-file-excel-o"></i>Export to Excel</a></li>
                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-content collapse show">
                            <div class="card-body card-dashboard">
                                <table class="table table-striped table-bordered text-inputs-searching file-export" id="ers-stats" data-name="ERS Stats as on {{date('d M, Y')}}">
                                    <thead>
                                    <tr>
                                        <th>Voucher #</th>
                                        <th>Employee #</th>
                                        <th>Employee Name</th>
                                        <th>Designation</th>
                                        <th>Employee Grade</th>
                                        <th>Cost Center</th>
                                        <th>Charged to Cost Center</th>
                                        <th>Expense Date</th>
                                        <th>Category</th>
                                        <th>Approved By</th>
                                        @foreach($columns as $column)
                                            @if(strpos($column, '_id') == false)
                                                <th>{{ucwords(str_replace('_', ' ',$column))}}</th>
                                            @endif
                                        @endforeach
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($voucherItems as $item)
                                        <tr>
                                            <td>{{$item->voucher->id}}</td>
                                            <td>{{$item->voucher->employee->employee_number}}</td>
                                            <td>{{$item->voucher->employee->employee_name}}</td>
                                            <td>{{$item->voucher->employee->designation}}</td>
                                            <td>{{$item->voucher->employee->employeeGrade->primary_name}}</td>
                                            <td>{{$item->voucher->employee->department->cost_center}}</td>
                                            <td>{{$item->voucher->charge_to_cost_center}}</td>
                                            <td data-sort="{{strtotime($item->date_from)}}">{{date('d M, Y', strtotime($item->date_from))}}</td>
                                            <td>{{$item->category->category_name}}</td>
                                            <td>
                                                @foreach($item->voucher->voucherStatus as $status)
                                                    @if(isset($status->pivot->approved))
                                                        @if($status->pivot->approved == 1)
                                                            <span class="badge badge-pill bg-success mb-0">Approved by {{$status->employee_name}}</span> <br>
                                                        @elseif($status->pivot->approved == 0)
                                                            <span class="badge badge-pill bg-danger mb-0">Rejected by {{$status->employee_name}}</span> <br>
                                                        @endif
                                                    @else
                                                        <span class="badge badge-pill bg-warning mb-0">Pending approval from {{$status->employee_name}}</span> <br>
                                                    @endif
                                                @endforeach
                                            </td>
                                            @foreach($columns as $column)
                                                @if(strpos($column, '_id') == false)
                                                    @if(isset($item->$column))
                                                        @if(strpos($column, 'date') !== false)
                                                            @if(date('H:i', strtotime($item->$column)) === '00:00')
                                                                <td data-sort="{{strtotime($item->$column)}}">{{date('d M, Y', strtotime($item->$column))}}</td>
                                                            @else
                                                                <td data-sort="{{strtotime($item->$column)}}">{{date('d M, Y H:i', strtotime($item->$column))}}</td>
                                                            @endif
                                                        @else
                                                            <td>{{$item->$column}}</td>
                                                        @endif
                                                    @else
                                                        <td>-</td>
                                                    @endif
                                                @endif
                                            @endforeach
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
    <script src="{{asset('assets/js/jquery.table2excel.js')}}"></script>
    <script>
        function exportTableToExcel(){
            var table = $('#ers-stats');
            if(table && table.length){
                var preserveColors = (table.hasClass('table2excel_with_colors') ? true : false);
                $(table).table2excel({
                    exclude: ".noExl",
                    name: "ERS Stats",
                    filename: "ERS Stats .xls",
                    fileext: ".xls",
                    exclude_img: true,
                    exclude_links: true,
                    exclude_inputs: true,
                    preserveColors: preserveColors
                });
            }
        }
        $('.table').dataTable({
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ]
        });
    </script>
@endsection