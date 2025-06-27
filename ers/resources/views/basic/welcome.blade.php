@extends('layouts.login-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/horizontal-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/wizard.css')}}">
    <style>
        input[type='number'] {
            -moz-appearance:textfield;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
        }
    </style>
@endsection
@section('body')
    @if (count($errors) > 0)
        <div class="alert bg-danger alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="la la-thumbs-o-down"></i></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>Oh !</strong> Please fix the following issues to continue
            <ul class="error">
                @foreach ($errors->all() as $error)
                    <li style="list-style: circle">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(Session::has("error"))
        <div class="alert bg-danger alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="la la-thumbs-o-down"></i></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>Oh !</strong> {{ Session::get("error") }}
        </div>
    @endif
    @if(Session::has("success"))
        <div class="alert bg-success alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="la la-thumbs-o-up"></i></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>Yeah !</strong> {{ Session::get("success") }}
        </div>
    @endif
    <div class="content-body">
        <section class="flexbox-container">
            <section id="validation">
                <div class="row">
                    <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header text-center">
                                <h1>Welcome, <br><strong class="primary darken-3">{{$employee->employee_name}}!</strong></h1>
                                <span>Sit tight and fill in the required information. You are responsible for your reimbursements!</span><br>
                                <span>Please complete the following steps to proceed further.</span>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <form action="{{URL::to('welcome')}}" id="welcome-form" method="POST" class="steps-validation wizard-circle">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <h6>Personal Information</h6>
                                        <fieldset>
                                            <div class="row mt-2">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control required" id="lastName4" value="{{Input::old('nickname')}}" name="nickname" placeholder="Nick Name">
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <input type="email" class="form-control required" id="firstName3" value="{{Input::old('email')}}" name="email" placeholder="Email Address">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input type="number" class="form-control required" id="lastName3" value="{{Input::old('mobile')}}" maxlength="11" name="mobile" placeholder="Mobile Number">
                                                        <small>e.g. 03004XXXXXX, without (-)</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control required" id="eventName3" value="{{Input::old('cnic')}}" maxlength="13" name="cnic" placeholder="CNIC Number">
                                                        <small>e.g. 35XXXXXXXXXXX, without (-)</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <h6>Bank Information</h6>
                                        <fieldset>
                                            <div class="row mt-2">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="emailAddress6" value="{{Input::old('bank_account_number')}}" maxlength="16" name="bank_account_number" placeholder="MCB Bank Account Number">
                                                        <small>e.g. 00000000XXXXXXXX (16 digit bank account number)</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <select class="form-select form-control" id="bank" name="bank" required>
                                                        <option value="">Select Bank</option>
                                                        @foreach($banks as $bank)
                                                            <option value="{{$bank->id}}">{{$bank->bank_name}}</option>
                                                        @endforeach
                                                    </select>
                                                    <small></small>
                                                </div>

                                            </div>
                                        </fieldset>
                                        <h6>Change Password</h6>
                                        <fieldset>
                                            <div class="row mt-2">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <input type="password" class="form-control" id="lastName3" value="{{Input::old('old_password')}}" name="old_password" placeholder="Old Password">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <input type="password" class="form-control" id="firstName3" value="{{Input::old('new_password')}}" name="new_password" placeholder="New Password">
                                                        <small>Your password must be more than <strong>8 characters long</strong>, should contain at-least <strong>1 Uppercase</strong>, <strong>1 Lowercase</strong>, <strong>1 Numeric</strong> and <strong>1 special character</strong>.</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <input type="password" class="form-control" id="eventName3" value="{{Input::old('new_password_confirmation')}}" name="new_password_confirmation" placeholder="Confirm New Password">
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <h6>Finish</h6>
                                        <fieldset>
                                            <hr>
                                            <div class="row mt-2">
                                                <div class="card-header">
                                                    <h1 class="success darken-3">Great!</h1>
                                                    <span>Now please 'Submit' to continue</span>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </section>
        </section>
    </div>
@endsection
@section('footer')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}" type="text/javascript"></script>
    <script type="text/javascript" src="{{asset('app-assets/vendors/js/ui/jquery.sticky.js')}}"></script>
    <script type="text/javascript" src="{{asset('app-assets/vendors/js/charts/jquery.sparkline.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/jquery.steps.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/dateTime/moment-with-locales.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/daterange/daterangepicker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script type="text/javascript" src="{{asset('app-assets/js/scripts/ui/breadcrumbs-with-stats.js')}}"></script>
    <script src="{{asset('app-assets/js/scripts/forms/wizard-steps.js')}}" type="text/javascript"></script>
@endsection