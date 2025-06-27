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
                    <form action="{{URL::to('store/travel-order/missing-details')}}" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="card" style="overflow-x: scroll;">
                            <div class="card-header">
                                <h4 class="card-title">Please Enter the GL Accounts & Conversion Rates First</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a class="export" data-table="sap-format"><i class="la la-file-excel-o"></i>Export to Excel</a></li>
                                        <!--<button class="btn btn-info btn-sm white select_all" onclick="selectAll()" id="select_all">Select All</button>-->
                                        <!--<li><a data-action="expand"><i class="ft-maximize"></i></a></li>-->
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body card-dashboard w-100 overflow-auto">
                                <table id="sap-format" class="table table-white-space table-bordered display no-wrap">
                                        <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Description</th>
                                            <th>G.L Account #</th>
                                            <th>Conversion Rate</th>
                                            <th>Forex Amount (USD)</th>
                                            <th>CC</th>
                                            <th>Emp #</th>
                                            <th>JV Description</th>
                                            <th>Action Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($voucherItems as $item)
                                            <tr>
                                                <td><a href="{{URL::to('travel-order/details'.'/'.$item->voucher->id)}}" target="_blank">{{$item->voucher->id}}</a></td>
                                                <td>{{$item->city.' / '.$item->country}}</td>
                                                <td>
                                                    <input type="text" class="form-control" name="gl_code[]" value="{{$item->gl_code}}" required>
                                                    <input type="text" class="form-control" name="item[]" value="{{$item->id}}" hidden>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="conversion_rate[]" value="{{$item->conversion_rate}}" required>
                                                </td>
                                                <td>{{$item->forex_amount}}</td>
                                                <td>{{$item->voucher->charge_to_cost_center}}</td>
                                                <td>{{$item->voucher->employee->employee_number}}</td>
                                                <td>{{$item->category->category_name}}</td>
                                                <td><input type="checkbox" class="form-control action_check" name="actions[]" value="{{$item->voucher->id}}"></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            <div class="card-footer text-right">
                                <button class="btn btn-success" type="submit">Save Information</button>
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
    <script>
        function selectAll()
        {
            var checkboxes = document.getElementsByClassName("action_check");
            for (i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = true;
            }
        }

    </script>
@endsection