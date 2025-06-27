@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
    <style>
        .content {
            background-color: #414042; /* Grey background */
        }
    </style>
@endsection
@section('body')
    <div class="content-body">
        <section id="form-control-repeater">
            <form action="{{URL::to('rejected/vouchers')}}" method="post">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="file-repeater">Rejected Vouchers Date</h4>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <div class="form row" id="igi-form">
                                        <div class="form-group col-md-4 mb-2">
                                            <label>Select from Date</label>
                                            <input type="date" class="form-control" value="{{Input::old('vmm_date')}}" name="vmm_date">
                                        </div>

                                        <div class="form-group col-md-4 mb-2">
                                            <label>Select to Date</label>
                                            <input type="date" class="form-control" value="{{Input::old('vmm_ending_date')}}" name="vmm_ending_date">
                                        </div>

                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                            <button type="submit" class="btn btn-success white btn-sm" id="submit"><i class="la la-paper-plane-o"></i> Generate </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection
@section('footer')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}" type="text/javascript"></script>
@endsection