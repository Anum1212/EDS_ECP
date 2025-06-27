@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
@endsection
@section('body')
    <div class="content-body">
        <section id="form-control-repeater">
            <form action="{{URL::to('profile')}}" method="post">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="file-repeater">Your Profile</h4>
                                <small>You can update your communication information here</small>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-h font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <div class="form row" id="bu-form">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <div class="form-group col-md-2 mb-2">
                                            <label>Employee #</label>
                                            <input type="text" class="form-control" value="{{$employee->employee_number}}" name="employee_number" disabled>
                                        </div>
                                        <div class="form-group col-md-3 mb-2">
                                            <label>Name</label>
                                            <input type="text" class="form-control" value="{{$employee->employee_name}}" name="employee_name" disabled>
                                        </div>
                                        <div class="form-group col-md-3 mb-2">
                                            <label>Designation</label>
                                            <input type="text" class="form-control" value="{{$employee->designation}}" name="designation" disabled>
                                        </div>
                                        <div class="form-group col-md-2 mb-2">
                                            <label>Grade</label>
                                            <input type="text" class="form-control" value="{{$employee->employeeGrade->primary_name}}" name="grade" disabled>
                                        </div>
                                        <div class="form-group col-md-2 mb-2">
                                            <label>CNIC</label>
                                            <input type="text" class="form-control" value="{{$employee->cnic}}" name="cnic" disabled>
                                        </div>
                                    </div>
                                    <div class="form row" id="bu-form">
                                        <div class="form-group col-md-5 mb-2">
                                            <label>Cost Center / Department / Division</label>
                                            <input type="text" class="form-control" value="{{$employee->department->cost_center.' / '.$employee->department->department_name.' / '.$employee->department->businessUnit->bu_name}}" name="division" disabled>
                                        </div>
                                        <div class="form-group col-md-3 mb-2">
                                            <label>Bank Account #</label>
                                            <input type="text" class="form-control" value="{{count($employee->bankAccounts)>0?$employee->bankAccounts[0]->pivot->account_number:""}}" name="account_number" disabled>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form row" id="bu-form">
                                        <div class="form-group col-md-2 mb-2">
                                            <label>Nick Name</label>
                                            <input type="text" class="form-control" value="{{$employee->nick_name}}" name="nick_name" required>
                                        </div>
                                        <div class="form-group col-md-4 mb-2">
                                            <label>Email Address</label>
                                            <input type="email" class="form-control" value="{{$employee->email}}" name="email" required>
                                        </div>
                                        <div class="form-group col-md-2 mb-2">
                                            <label>Contact #</label>
                                            <input type="text" class="form-control" value="{{$employee->mobile}}" name="mobile" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer" id="bu-form-footer">
                                    <button class="btn btn-success btn-sm" type="submit"><i class="la la-paper-plane-o"></i> Submit </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        @if(view()->exists($customView))
                            @include($customView)
                        @endif
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