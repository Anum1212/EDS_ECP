@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/extensions/colReorder.dataTables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/extensions/buttons.dataTables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/extensions/fixedHeader.dataTables.min.css')}}">
    <style>
        .content {
            background-color: #414042; /* Grey background for the content */
        }
        .table thead th {
            background-color: #e9ecef; /* Light gray background for table headers */
            color: #333; /* Darker text for contrast */
        }
        .badge {
            font-size: 0.875rem;
        }
        .table th, .table td {
            vertical-align: middle;
            padding: 0.75rem;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1; /* Light hover effect for table rows */
        }
    </style>
@endsection
@section('body')
    <div class="content-body">
        <section id="configuration">
            <div class="row justify-content-center">
                <div class="col-10">
                    <form action="{{URL::to('store/gl/failures')}}" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="card" style="overflow-x: scroll;">
                            <div class="card-header">
                                <h4 class="card-title">Please Enter the GL Accounts First</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a class="export" data-table="sap-format"><i class="la la-file-excel-o"></i>Export to Excel</a></li>
                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body card-dashboard">
                                    <table id="sap-format" class="table table-white-space table-bordered display no-wrap">
                                        <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>G.L Account #</th>
                                            <th>Litres</th>
                                            <th>Rate/Litre</th>
                                            <th>Amount</th>
                                            <th>Order Number</th>
                                            <th>CC</th>
                                            <th>Emp #</th>
                                            <th>JV Description</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($voucherItems as $item)
                                            <tr>
                                                <td>{{$item->description}}</td>
                                                <td>
                                                    <input type="text" class="form-control" name="gl_code[]" required>
                                                    <input type="text" class="form-control" name="item[]" value="{{$item->id}}" hidden>
                                                </td>
                                                <td>{{$item->litres}}</td>
                                                <td>{{$item->rate_per_litre}}</td>
                                                <td>{{$item->amount}}</td>
                                                <td>{{$item->vehicle_number}}</td>
                                                <td>{{$item->voucher->charge_to_cost_center}}</td>
                                                <td>{{$item->voucher->employee->employee_number}}</td>
                                                <td>{{$item->category->category_name}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <button class="btn btn-success" type="submit">Save GL Accounts</button>
                            </div>
                        </div>
                    </form>
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
    <script src="{{asset('assets/js-xlsx/xlsx.core.js')}}"></script>
    <script src="{{asset('assets/file-saver/FileSaver.min.js')}}"></script>
    <script src="{{asset('assets/table-export/js/tableexport.js')}}"></script>
@endsection