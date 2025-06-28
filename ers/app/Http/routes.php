<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => ['Authenticate', 'HttpsProtocol', 'AccountsAuthentication']],function () {
    
    Route::get('testing/stats','VoucherController@reject_vouchers_ers');
    
    Route::get('extract/stats', 'ReportsController@stats');
    Route::post('extract/stats', 'ReportsController@extractStats');

    Route::get('category/update/{id}', 'CategoryController@edit');
    Route::get('employees', 'EmployeeController@index');

    Route::get('division/add', 'BusinessUnitController@create');
    Route::post('division/add', 'BusinessUnitController@store');
    Route::get('divisions', 'BusinessUnitController@index');

    Route::get('department/add', 'DepartmentController@create');
    Route::post('department/add', 'DepartmentController@store');
    Route::get('departments', 'DepartmentController@index');

    Route::get('category/add', 'CategoryController@create');
    Route::post('category/add', 'CategoryController@store');
    Route::get('categories', 'CategoryController@index');

    Route::get('gl-account/add', 'GLController@create');
    Route::post('gl-account/add', 'GLController@store');
    Route::get('gl-accounts', 'GLController@index');

    Route::get('rate/add', 'RateController@create');
    Route::post('rate/add', 'RateController@store');
    Route::get('rate', 'RateController@index');

    Route::get('voucher/documents/received/{id}', 'VoucherController@documentsReceived');
    Route::post('voucher/documents/received', 'VoucherController@documentsReceived');

    Route::get('categories', 'CategoryController@index');
    Route::get('rates', 'RateController@index');

    Route::get('home', 'HomeController@index');

    Route::get('process/vouchers', 'VoucherController@processVoucher');
    Route::get('process/travel-orders', 'VoucherController@processTravelOrders');
    Route::post('process/travel-orders', 'VoucherController@storeProcessTravelOrders');

    Route::get('processed/vouchers', 'VoucherController@processedVouchers');
    Route::post('processed/vouchers', 'VoucherController@processedVouchers');
    Route::get('generate/export/formats/{filterBank}/{filterColumn}/{from}/{to}', 'VoucherController@generateExportFormats');

    Route::get('employee/add', 'EmployeeController@create');
    Route::post('employee/add', 'EmployeeController@store');
    Route::get('employee/update/{id}', 'EmployeeController@edit');
    Route::post('employee/update/{id}', 'EmployeeController@update');

    Route::post('documents/objection/{id}', 'VoucherController@documentsObjection');

    Route::get('rejected/vouchers', 'VoucherController@rejectedVouchers');
    Route::post('rejected/vouchers', 'VoucherController@rejectedVouchersSelect');
    Route::get('remove/rejection/{id}', 'VoucherController@removeRejection');

    Route::post('store/gl/failures', 'VoucherController@storeGLFailures');
    Route::post('store/travel-order/missing-details', 'VoucherController@storeTravelOrderMissingDetails');

    Route::get('correct/postings', 'VoucherController@correctPostings');

    Route::get('vouchers/status/update', 'VoucherController@updateStatusIndividual');
    Route::get('vouchers/status/update/all', 'VoucherController@updateStatus');
});

Route::group(['middleware' => ['Authenticate', 'HttpsProtocol']],function () {

	Route::get('travel-order/add', 'VoucherController@create');
	Route::post('travel-order/add', 'VoucherController@store');
	Route::get('travel-order/details/{id}', 'VoucherController@show');
	Route::get('travel-orders', 'VoucherController@index');
	Route::get('travel-orders/approved', 'VoucherController@approvedVouchers');
	Route::get('travel-orders/unapproved', 'VoucherController@unapprovedVouchers');
	Route::get('travel-order/send/approval/{id}', 'VoucherController@sendApproval');
	Route::post('travel-order/first/approval/{id}', 'VoucherController@firstVoucherApproval');
    Route::get('travel-order/first/approval/{id}', 'VoucherController@firstVoucherApproval');
	Route::post('travel-order/first/approval/bu-wise/{id}', 'VoucherController@firstVoucherApprovalBUApproverWise');
    Route::get('travel-order/first/approval/bu-wise/{id}', 'VoucherController@firstVoucherApprovalBUApproverWise');
    Route::get('travel-order/status/update', 'TravelOrderController@updateStatus');

    Route::post('get/voucher/details', 'VoucherController@getVoucherDetails');
    Route::get('dashboard', 'DashboardController@index');
    Route::get('profile', 'EmployeeController@profile');
    Route::post('profile', 'EmployeeController@storeProfile');

    Route::get('voucher/add', 'VoucherController@create');
    Route::get('medical-claim/add', 'VoucherController@create');
    Route::get('fuel-claim/add', 'VoucherController@create');
    Route::get('fuel-mileage/add', 'VoucherController@create');
    Route::get('travel-claim/add', 'VoucherController@create');
    Route::get('voucher/add/{id}', 'VoucherController@createVoucherItems');
    Route::post('voucher/add/{id}', 'VoucherController@storeVoucherItems');
    Route::post('voucher/add/company/{id}', 'VoucherController@create');

    Route::post('feedback', 'EmployeeController@feedback');

    Route::post('voucher/add', 'VoucherController@store');
    Route::get('voucher/details/{id}', 'VoucherController@show');
    Route::get('vouchers', 'VoucherController@index');
    Route::get('vouchers/approved', 'VoucherController@approvedVouchers');
    Route::get('vouchers/unapproved', 'VoucherController@unapprovedVouchers');

	Route::get('voucher/item/delete/{id}', 'VoucherController@deleteVoucherItem');
    Route::post('voucher/first/approval/{id}', 'VoucherController@firstVoucherApproval');
    Route::get('voucher/first/approval/{id}', 'VoucherController@firstVoucherApproval');
    Route::post('voucher/first/approval/bu-wise/{id}', 'VoucherController@firstVoucherApprovalBUApproverWise');
    Route::get('voucher/first/approval/bu-wise/{id}', 'VoucherController@firstVoucherApprovalBUApproverWise');

    Route::get('voucher/send/approval/{id}', 'VoucherController@sendApproval');

    Route::get('vouchers/require/approval', 'VoucherController@requireApproval');
    Route::get('vouchers/approver/approved', 'VoucherController@approverApproved');
    Route::get('vouchers/approver/declined', 'VoucherController@approverDeclined');

    Route::get('voucher/approve/{id}', 'VoucherController@approveVoucher');
    Route::get('travel-order/approve/{id}', 'VoucherController@approveVoucher');

    Route::post('voucher/approve/{id}', 'VoucherController@secondVoucherApproval');
    Route::post('travel-order/approve/{id}', 'VoucherController@secondVoucherApproval');

    Route::post('voucher/reject/{id}', 'VoucherController@rejectVoucher');
    Route::post('travel-order/reject/{id}', 'VoucherController@rejectVoucher');

    Route::post('make/voucher/copy', 'VoucherController@makeCopy');

    Route::get('voucher/delete/{id}', 'VoucherController@destroy');

    Route::get('voucher/medicines-mother-claim/', 'VoucherController@showMedicinesMotherFormVoucher');
    Route::post('voucher/medicines-mother-claim/create', 'VoucherController@createMedicinesMotherVoucher');
    Route::get('voucher/medicines-mother-claim/submit/{id}', 'VoucherController@submitMedicinesMotherVoucher');
    Route::get('voucher/approve-medicines-mother/{id}', 'VoucherController@approveMedicinesMotherVoucher');
    Route::post('voucher/approve-medicines-mother/{id}', 'VoucherController@approveMedicinesMotherVoucher');
    
    Route::post('voucher/approve-medical-opd/{id}', 'VoucherController@approveMedicalOPDVoucher');
    
    Route::get('process/medical/vouchers', 'VoucherController@processMedicalVoucher');
    Route::post('process/vouchers', 'VoucherController@storeProcessVoucher');

    // mess booking routes
    Route::get('mess-booking/add', 'MessBookingController@showMessBookingForm');
    Route::get('mess-bookings/{userType}/{listType?}', 'MessBookingController@showMessBookings');
    Route::get('mess-booking/details/{id}/{userType?}', 'MessBookingController@showMessBookingDetails');
    Route::post('mess-booking/create', 'MessBookingController@createMessBooking');
    Route::get('mess-booking/submit/{id}', 'MessBookingController@submitMessBooking');
    Route::get('mess-booking/delete/{id}', 'MessBookingController@deleteMessBooking');
    Route::get('mess-booking/approve/{id}', 'MessBookingController@approveMessBooking');
    Route::post('mess-booking/reject/{id}', 'MessBookingController@rejectMessBooking');
    Route::post('mess-booking/report', 'MessBookingController@generateReport');
});

Route::group(['middleware' => ['HttpsProtocol']],function () {
	//login functionality
	Route::get('/', 'UserController@login');
	Route::post('login', 'UserController@doLogin');
    Route::post('sf-login', 'UserController@sfLoginAPI');
	Route::get('logout', 'UserController@logout');
	
	Route::get('testing/dic','VoucherController@process_vouchers_dic');

	Route::get('forget/password', 'UserController@forgetPassword');
	Route::post('forget/password', 'UserController@sendPasswordResetLink');
	Route::get('forget/password/{token}', 'UserController@verifyToken');
	Route::post('reset/password/{token}', 'UserController@resetPassword');

	//login API for other applications
	Route::get('login/{key}', 'UserController@loginAPI');
	Route::get('login/groupapp/{key}', 'UserController@GroupAppLoginAPI');
	Route::post('client/login', 'UserController@clientLogin');

	//welcome window
	Route::get('welcome', 'UserController@welcome');
	Route::post('welcome', 'UserController@storeWelcomeData');

	Route::get('voucher/category/form/display/{category}/parent/{parent}/item/{itemCount}/id/{categoryID}', 'VoucherController@formDisplay');
	Route::get('item/add/{itemCount}/parent/{parent}/id/{categoryID}', 'VoucherController@addVoucherItem');
	Route::get('voucher/category/add/{count}', 'VoucherController@addVoucherCategory');
	Route::get('get/rate/{categoryName}/{from}', 'RateController@getCategoryRate');
	Route::get('get/all/expense/graph/data', 'VoucherController@getExpenseGraph');
	Route::get('partials/add/vehicle', 'EmployeeController@addPartialVehicle');

	Route::post('calculate/days', 'VoucherController@calculateDays');
	Route::post('check/gl-mapping', 'GLController@getGLMapping');

	Route::get('test/api', 'VoucherController@testApi');
	Route::get('calculate/forex-to-local/{from}', 'RateController@forexToLocal');
	
});

Route::get('sap/processed/{id}/jv/{jvNumber}','VoucherController@sapProcessed');
Route::post('sap/update/employee', 'EmployeeController@sapUpdate');
Route::post('sap/update/leavers', 'EmployeeController@sapUpdateLeavers');
Route::post('sap/update/employee/family', 'EmployeeController@sapUpdateEmployeeFamily');
Route::post('send/email/api', 'MailController@sendMailApi');

//Route::get('send-voucher-email','VoucherController@test')

Route::get('check/api', 'DashboardController@checkApi');
Route::post('check/api', 'DashboardController@storeCheckApi');

Route::post('sap/update/employee/testing', 'EmployeeController@sapUpdateTesting');
Route::get('update/data', 'DashboardController@updateData');

Route::get('maintenance/email', 'UserController@maintenanceEmail');
Route::get('maintenance', 'DashboardController@maintenance');
Route::get('testmail', 'VoucherController@testmail');

// For Travel Order Reports
Route::get('travel-order/report','VoucherController@travel_order_report');
Route::post('travel-order/report','VoucherController@travel_order_report');


Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('sap-s4-sync', 'UserController@sapS4sync');

Route::get('sync-bu-head', 'UserController@fetch_bu_heads');

Route::get('/test-email', function () {
    try {
        \Mail::raw('Test email', function ($message) {
            $message->to('uzair.mazhar@packages.com.pk')
                    ->subject('Test Mail');
        });

        return 'Mail sent';
    } catch (\Exception $e) {
        return 'Mail failed: ' . $e->getMessage();
    }
});

