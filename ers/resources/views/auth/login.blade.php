@extends('layouts.login-layout')

@section('header')
<style>
    body {
        font-family: "72", Arial, Helvetica, sans-serif; /* Set the font-family for the body */
    }
    footer.footer-sf {
        /* position: fixed; */
        bottom: 0;
        width: 100%;
        padding: 10px 20px;
        background-color: #414042 !important; /* Footer background with important */
        color: lightgrey;
        text-align: left;
        font-size: 0.8rem; /* Small font size */
        display: flex;
        justify-content: space-between; /* Align the text on the left and image on the right */
        align-items: center;
    }
    .footer-logo {
        max-width: 250px; /* Adjust the size of the image */
        height: auto;
    }
    .form-control {
        width: 100%;
        padding: 12px; /* Increase padding for a better feel */
        font-size: 1rem; /* Slightly increase font size */
        background-color: black;
        color: white;
        border: 1px solid #717E8B; /* Optional: Add a border for better visibility */
        border-radius: 8px; /* Optional: Slightly rounded edges */
    }
</style>
@endsection

@section('body')
<div class="content-body" style="background-color: #414042; min-height: 100vh;">
    <section class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-6 col-sm-10 text-center">
                    <div class="p-4">
                        <!-- Logo -->
                        <img src="{{asset('assets/img/logo.jpeg')}}" alt="Company Logo" class="img-fluid mb-3" style="max-width: 100%; height: auto;">

                        <!-- Text -->
                        <p class="text-white text-justify mb-4" style="font-size: 1rem;">
                            Please enter your <strong>SF</strong> credentials to login.
                        </p>

                        <!-- Form -->
                        <form action="{{ URL::to('sf-login') }}" method="post">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            @if(Session::has("error"))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <strong>Warning:</strong> {{ Session::get("error") }}
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                </div>
                            @endif

                            @if(Session::has("success"))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get("success") }}
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                </div>
                            @endif

                            <!-- Username -->
                            <div class="form-group text-left">
                                <label for="username" class="text-light" style="color: #717E8B;">Username</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your Username" value="{{ old('username') }}" style="background-color: black; color: white;">
                            </div>

                            <!-- Password -->
                            <div class="form-group text-left">
                                <label for="password" class="text-light" style="color: #717E8B;">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your Password" style="background-color: black; color: white;">
                            </div>
                            
                            <!-- Forgot Password Link -->
                            <div class="form-group text-right">
                                <b><a href="https://hcm44.sapsf.com/login#/passwordHelp" style="color: white; font-size: 1rem; text-decoration: underline;">Forgot Password?</a></b>
                            </div>
                            
                            <!-- Login Button -->
                            <button type="submit" class="btn btn-block" style="background-color: #0789FA; color: black; border-radius: 25px;">

                                <i class="ft-unlock"></i> Login
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Footer -->
<footer class="footer-sf d-flex flex-column flex-md-row align-items-center justify-content-between">
    <span class="text-center text-md-left">
        © 2024 SAP SuccessFactors, Inc. All rights reserved. 
        <a href="#" class="text-muted text-decoration-underline">Show version info</a>
    </span>
    <img src="{{ asset('assets/img/sf-logo.png') }}" alt="Footer Image" class="footer-logo img-fluid">
</footer>
@endsection

@section('footer')
@endsection
