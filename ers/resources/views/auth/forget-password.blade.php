@extends('layouts.login-layout')
@section('header')
@endsection
@section('body')
    <div class="content-body">
        <section class="flexbox-container">
            <div class="col-12 d-flex align-items-center justify-content-center">
                <div class="col-md-4 col-10 box-shadow-2 p-0">
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
                    <div class="card border-grey border-lighten-3 m-0">
                        <div class="card-header border-0">
                            <div class="card-title text-center">
                                <div class="p-1">
                                    <img src="{{asset('assets/img/logo-packages.png')}}" alt="branding logo">
                                </div>
                            </div>
                        </div>
                        <div class="card-content">
                            <p class="card-subtitle line-on-side text-muted text-center font-small-3 mx-2">
                                <span>Forget Password</span>
                            </p>
                            <div class="card-body pt-0">
                                <form class="form-horizontal" action="{{URL::to('forget/password')}}" method="post">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <fieldset class="form-group floating-label-form-group">
                                        <label for="user-name">Please Enter Your Registered Employee ID</label>
                                        <input type="text" class="form-control" id="username" name="username" value="{{Input::old('username')}}" placeholder="Your Employee ID">
                                    </fieldset>
                                    <button type="submit" class="btn btn-outline-info btn-block"><i class="ft-unlock"></i> Send Password Reset Link</button>
                                    <div class="form-group col-md-6" hidden>
                                        <input type="text" class="form-control" id="ipAddress" style="color: black;" placeholder="IP Address" name="ipAddress" readonly required hidden>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('footer')
    <script type="text/javascript" src="{{asset('assets/js/secure-me.js')}}"></script>
    <script type="text/javascript">

    </script>
@endsection