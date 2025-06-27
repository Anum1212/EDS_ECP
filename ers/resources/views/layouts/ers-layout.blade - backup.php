<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Employee Reimbursement System - Packages Group">
    <meta name="keywords" content="voucher, travelling, allowances">
    <meta name="author" content="PIXINVENT">
    <title>SAP - Global Benefits</title>
    <link rel="apple-touch-icon" href="{{asset('assets/img/SF.ico')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets/img/SF.ico')}}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700" rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/vendors.css')}}">
    <!-- END VENDOR CSS-->
    <!-- BEGIN MODERN CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/app.css')}}">
    <!-- END MODERN CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/horizontal-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/colors/palette-gradient.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/charts/jquery-jvectormap-2.0.3.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/charts/morris.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/colors/palette-gradient.css')}}">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}">
    <style>
        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu .dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -1px;
            display: none;
        }

        .dropdown-submenu:hover .dropdown-menu {
            display: block;
        }

        /* Optional: Add some styling for dropdown items */
        .dropdown-item {
            padding: 8px 16px;
            text-decoration: none;
            display: block;
            color: #000;
        }

        .dropdown-item:hover {
            background-color: #f1f1f1;
        }

        .tile-text{
            color: black !important;
        }
    </style>
    <!-- END Custom CSS-->
    @yield('header')
</head>
<body class="horizontal-layout horizontal-menu 2-columns   menu-expanded" data-open="hover"
      data-menu="horizontal-menu" data-col="2-columns">
<!-- fixed-top-->
<nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow navbar-static-top navbar-light navbar-brand-center">
    <div class="navbar-wrapper">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mobile-menu d-md-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu font-large-1"></i></a></li>
                {{-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Select Option
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">Option 1</a>
                        <a class="dropdown-item" href="#">Option 2</a>
                        <a class="dropdown-item" href="#">Option 3</a>
                    </div>
                </li> --}}
                <li class="nav-item">
                    <a class="navbar-brand" href="{{URL::to('dashboard')}}">
                        <img class="brand-logo" style="width: 70px" alt="SAP logo" src="{{asset('assets/img/SAP-logo.png')}}">
                        <h3 class="brand-text">Global Benefits</h3>
                    </a>
                </li>
                <li class="nav-item d-md-none">
                    <a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i class="la la-ellipsis-v"></i></a>
                </li>
            </ul>
        </div>
        <div class="navbar-container content">
            <div class="collapse navbar-collapse" id="navbar-mobile">
                <ul class="nav navbar-nav mr-auto float-left">
                    <li class="nav-item d-none d-md-block"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu"></i></a></li>
                    <li class="nav-item d-none d-md-block"><a class="nav-link nav-link-expand" href="#"><i class="ficon ft-maximize"></i></a></li>
                </ul>
                <ul class="nav navbar-nav float-right">
                    <li class="dropdown dropdown-user nav-item">
                        <a class="dropdown-toggle nav-link dropdown-user-link dropdown-item" href="#" data-toggle="dropdown"> 
                            <span class="mr-1">Home</span> 
                        </a> 
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{URL::to('dashboard')}}">Home</a>
                            <!-- Claims with sub-items -->
                            <div class="dropdown dropdown-submenu">
                                <a class="dropdown-item dropdown-toggle" href="{{URL::to('#')}}">Claims</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{URL::to('voucher/add')}}">New Claim</a></li>
                                    <li><a class="dropdown-item" href="{{URL::to('vouchers/approved')}}">Approved Claims</a></li>
                                    <li><a class="dropdown-item" href="{{URL::to('vouchers/unapproved')}}">Unapproved Claims</a></li>
                                    <li><a class="dropdown-item" href="{{URL::to('vouchers')}}">All Claims</a></li>
                                </ul>
                            </div>
                
                            <!-- Travel Orders with sub-items -->
                            <div class="dropdown dropdown-submenu">
                                <a class="dropdown-item dropdown-toggle" href="{{URL::to('logout')}}">Travel Orders</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{URL::to('travel-orders/add')}}">New Travel Orders</a></li>
                                    <li><a class="dropdown-item" href="{{URL::to('travel-orders/approved')}}">Approved Travel Orders</a></li>
                                    <li><a class="dropdown-item" href="{{URL::to('travel-orders/unapproved')}}">Unapproved Travel Orders</a></li>
                                    <li><a class="dropdown-item" href="{{URL::to('travel-orders')}}">All Travel Orders</a></li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown dropdown-user nav-item">
                        <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                            <span class="mr-1">Hello,
                              <span class="user-name text-bold-700" >{{$employee->nick_name}}</span>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{URL::to('profile')}}"><i class="ft-user"></i> Profile</a>
                            <a class="dropdown-item" href="{{URL::to('logout')}}"><i class="ft-power"></i> Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
</nav>
<div class="header-navbar navbar-expand-sm navbar navbar-horizontal navbar-fixed navbar-dark navbar-without-dd-arrow navbar-shadow" role="navigation" data-menu="menu-wrapper">
    <div class="navbar-container main-menu-content" data-menu="menu-container">
        <ul class="nav navbar-nav" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item {{$path == 'dashboard' ? 'active':""}}">
                <a class="nav-link" href="{{URL::to('dashboard')}}"><i class="la la-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown"><i class="la la-th-list"></i><span>Vouchers</span></a>
                <ul class="dropdown-menu">
                    <li data-menu="" class="{{$path == 'voucher/add' ? 'active':""}}">
                        <a class="dropdown-item" href="{{URL::to('voucher/add')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>New Claim</a>
                    </li>
                    <li data-menu="" class="{{$path == 'vouchers/approved' ? 'active':""}}">
                        <a class="dropdown-item" href="{{URL::to('vouchers/approved')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Approved Claims</a>
                    </li>
                    <li data-menu="" class="{{$path == 'vouchers/unapproved' ? 'active':""}}">
                        <a class="dropdown-item" href="{{URL::to('vouchers/unapproved')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Unapproved Claims</a>
                    </li>
                    <li data-menu="" class="{{$path == 'vouchers' ? 'active':""}}">
                        <a class="dropdown-item" href="{{URL::to('vouchers')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>All Claims</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown"><i class="la la-file"></i><span>Travel Orders</span></a>
                <ul class="dropdown-menu">
                    <li data-menu="" class="{{$path == 'travel-order/add' ? 'active':""}}">
                        <a class="dropdown-item" href="{{URL::to('travel-order/add')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>New Travel Order</a>
                    </li>
                    <li data-menu="" class="{{$path == 'travel-orders/approved' ? 'active':""}}">
                        <a class="dropdown-item" href="{{URL::to('travel-orders/approved')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Approved Travel Orders</a>
                    </li>
                    <li data-menu="" class="{{$path == 'travel-orders/unapproved' ? 'active':""}}">
                        <a class="dropdown-item" href="{{URL::to('travel-orders/unapproved')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>Unapproved Travel Orders</a>
                    </li>
                    <li data-menu="" class="{{$path == 'travel-orders' ? 'active':""}}">
                        <a class="dropdown-item" href="{{URL::to('travel-orders')}}" data-toggle="dropdown"><i class="la la-angle-right"></i>All Travel Orders</a>
                    </li>
                </ul>
            </li>
            @if(View()->exists($customLayout))
                @include($customLayout)
            @endif
        </ul>
    </div>
</div>
<div class="app-content content">
    <div class="content-wrapper">
        <div id="jsMessage"></div>
        @if (count($errors) > 0)
            <div class="alert bg-danger alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                <span class="alert-icon"><i class="la la-thumbs-o-down"></i></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>Oh!</strong> Please fix the following issues to continue
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
                <strong>Oh!</strong> {{ Session::get("error") }}
            </div>
        @endif
        @if(Session::has("systemError"))
            <div class="alert bg-danger alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                <span class="alert-icon"><i class="la la-thumbs-o-down"></i></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>Oh! Please contact system administrator.</strong><br>{{ Session::get("systemError") }}<br><br><strong>Sorry for Inconvenience</strong>.
            </div>
        @endif
        @if(Session::has("success"))
            <div class="alert bg-success alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                <span class="alert-icon"><i class="la la-thumbs-o-up"></i></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>Yeah!</strong> {{ Session::get("success") }}
            </div>
        @endif
        @yield('body')
    </div>
</div>
<footer class="footer footer-static footer-light navbar-shadow">
    <p class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2">
      <span class="float-md-left d-block d-md-inline-block">Copyright © 2024 SAP SuccessFactors, Inc. All rights reserved. These online services are SuccessFactors confidential and proprietary and for use by authorized SuccessFactors customers only.</span>
    </p>
</footer>
<!-- BEGIN VENDOR JS-->
<script src="{{asset('app-assets/vendors/js/vendors.min.js')}}" type="text/javascript"></script>
<!-- BEGIN VENDOR JS-->
<!-- BEGIN PAGE VENDOR JS-->
<script type="text/javascript" src="{{asset('app-assets/vendors/js/ui/jquery.sticky.js')}}"></script>
<script type="text/javascript" src="{{asset('app-assets/vendors/js/charts/jquery.sparkline.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/charts/chart.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/charts/raphael-min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/charts/morris.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/charts/jvector/jquery-jvectormap-2.0.3.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/charts/jvector/jquery-jvectormap-world-mill.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/data/jvector/visitor-data.js')}}" type="text/javascript"></script>
<!-- END PAGE VENDOR JS-->
<!-- BEGIN MODERN JS-->
<script src="{{asset('app-assets/js/core/app-menu.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/js/core/app.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/js/scripts/customizer.js')}}" type="text/javascript"></script>
<!-- END MODERN JS-->
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript" src="{{asset('app-assets/js/scripts/ui/breadcrumbs-with-stats.js')}}"></script>
<!-- END PAGE LEVEL JS-->
@yield('footer')
</body>
</html>