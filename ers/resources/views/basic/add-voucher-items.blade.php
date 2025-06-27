@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/daterange/daterangepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/custom/daterangepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/custom/air-date-picker/css/datepicker.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/custom/vouchers.css')}}">
@endsection
@section('body')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">Adding Expense Items to Voucher # {{$voucher->id}}</h3>
            <p>Here you can add your new voucher</p>
        </div>
    </div>
    <div class="content-body">
        <section id="form-control-repeater">
            <form action="{{URL::to('voucher/add'.'/'.$voucher->id)}}" method="post" id="voucher-form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-success">
                                <h4 class="card-title white" id="file-repeater">Your Profile</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-h font-medium-3"></i></a>
                            </div>
                            <div class="card-body card-dashboard w-100 overflow-auto">
                                <table class="table table-striped">
                                        <tbody>
                                        <tr>
                                            <td class="text-right"><strong>ID / Grade: </strong></td>
                                            <td>{{$employee->employee_number.' / '.$employee->employeeGrade->primary_name}}</td>
                                            <td class="text-right"><strong>Name: </strong></td>
                                            <td>{{$employee->employee_name}}</td>
                                            <td class="text-right"><strong>Department: </strong></td>
                                            <td>{{$employee->department->department_name}}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right"><strong>Cost Center: </strong></td>
                                            <td>{{$employee->department->cost_center}}</td>
                                            <td class="text-right"><strong>Mobile # </strong></td>
                                            <td>{{$employee->mobile}}</td>
                                            <td class="text-right"><strong>Bank Account # </strong></td>
                                            <td>
                                                @foreach($employee->bankAccounts as $bankAccount)
                                                    {{$bankAccount->bank_name.' - '.$bankAccount->pivot->account_number}}
                                                @endforeach
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-primary white">
                                <h4 class="card-title white" id="file-repeater">Enter Details</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-h font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <input type="hidden" id="categoriesDetailCount" name="categoriesDetailCount" value="1">
                                    <input type="hidden" id="categoriesCount" name="categoriesCount" value="1">
                                    <input type="hidden" id="itemCount" name="itemCount" value="0">
                                    <div id="rowContainer">
                                        <div class="form-row">
                                            <div class="form-group col-md-4 mb-2">
                                                <select class="select2 form-control voucherCategory" name="voucher_categories_1" id="voucher_categories_1" required>
                                                    <option value="">Select Category</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{$category->id}}" data-view="{{$category->view}}">{{$category->category_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2 mb-2">
                                                <input type="text" class="form-control" name="total_voucher_categories_1" id="total_voucher_categories_1" placeholder="Total Amount (PKR)" readonly>
                                            </div>
                                            <div class="col-md-2">
                                                <a class="btn btn-danger btn-sm white" id="removeCategory"><i class="la la-trash-o"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="categoriesContainer"></div>
                                </div>
                                <div class="card-footer" id="company-form-footer">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a class="btn btn-info btn-sm white" id="addCategory"><i class="la la-plus-circle"></i> Add Category</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer" id="company-form-footer">
                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                            <button type="submit" class="btn btn-success white btn-sm" id="submit"><i class="la la-paper-plane-o"></i> Save </button>
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
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.time.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/dateTime/moment-with-locales.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/daterange/daterangepicker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/custom/daterangepicker.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/custom/date.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/custom/air-date-picker/js/datepicker.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/custom/air-date-picker/js/i18n/datepicker.en.js')}}" type="text/javascript"></script>

    <script>
        var categoriesDetailCount = 1;
        var itemCount = -1;
        var categoriesCount = 1;
        $('body').on('change','select.voucherCategory', function(evt){
            var category = $(this).find(':selected').data('view');
            var categoryContainer = $(this);
            var categoryID = $(this).val();
            evt.preventDefault();
            categoriesDetailCount = categoriesDetailCount+1;
            itemCount = itemCount+1;
            $('#categoriesDetailCount').val(categoriesDetailCount);
            var element_id = $(this).attr('id');
            if($("[data-parent='" + element_id + "']").length){
                alert('Details already exist against this selection. Please add more selection.');
            }
            else{
                if($('#'+category).length){
                    alert('Oh! This Category Already Exist.');
                }
                else{
                    $.ajax({
                        url: "{{ URL::to('voucher/category/form/display') }}/" + category + "/parent/" + element_id + '/item/' + itemCount + '/id/' + categoryID,
                        method:"GET",
                        data:{
                            type: "{!! $type !!}"
                        },
                        success:function (response){
                            $("#rowContainer").append(JSON.parse(response).row);
                            $("#itemCount").val(itemCount);
                            categoryContainer.attr('disabled', true);
                            if(category == 'hotel-stay-pakistan'){
                                $('.datetimeHotelStay').daterangepicker({
                                    timePicker: false,
                                });
                            }
                            else if(category == 'out-of-lahore-meal' || category == 'out-of-office-meal'){
                                $('.mealOutOfLahore').datepicker({
                                    language: 'en',
                                    multipleDates: true
                                });
                            }
                            else{
                                $('.datetime').daterangepicker({
                                    timePicker: true,
                                    timePicker24Hour: true,
                                    timePickerIncrement: 1,
                                    maxSpan:3,
                                    locale: {
                                        format: 'MM/DD/YYYY HH:mm'
                                    }
                                });
                            }
                        }
                    });
                }
            }
        }).on('click', '#addVoucherItem', function(){
            itemCount = itemCount+1;
            var element_id = $(this).data('category');
            var categoryID = $(this).data('category-id');
            $.ajax({
                url: "{{ URL::to('item/add') }}/" + itemCount + "/parent/" + element_id + '/id/' + categoryID,
                method:"GET",
                data:{
                    type: "{!! $type !!}"
                },
                success:function (response){
                    $("#"+element_id).closest('div').find('.card-body').append(JSON.parse(response).row);
                    $("#itemCount").val(itemCount);
                    if(element_id == 'hotel-stay-pakistan'){
                        $('.datetime').daterangepicker({
                            timePicker: false,
                        });
                    }
                    else if(element_id == 'out-of-lahore-meal' || element_id == 'out-of-office-meal'){
                        $('.mealOutOfLahore').datepicker({
                            language: 'en',
                            multipleDates: true
                        });
                    }
                    else{
                        $('.datetime').daterangepicker({
                            timePicker: true,
                            timePicker24Hour: true,
                            timePickerIncrement: 1,
                            locale: {
                                format: 'MM/DD/YYYY HH:mm'
                            }
                        });
                    }
                }
            });
        }).on('change', "input[name^='kms_']", function(){
            var category = $('#'+$(this).closest('div.card').data('parent')+' option:selected').text();
            var totalAmountContainer = $('#total_'+$(this).closest('div.card').data('parent'));
            if(category == 'Fuel - Mileage (within city)' || category == 'Fuel - Mileage (out of city)'){
                var kms = $(this).val();
                var from =  $(this).closest('div.form-row').find("input[name^='date_']").val();

                var amount = 0;
                var amountContainer = $(this).closest('div.form-row').find("input[name^='amount_']");
                var mode_of_travel = $(this).closest('div.form-row').find("select[name^='mode_of_travel_']").val();
                var totalAmount = totalAmountContainer.val()-amountContainer.val();
                $.ajax({
                    url: "{{URL::to('get/rate')}}/" + mode_of_travel + "/" + from,
                    success:function(response){
                        var res = JSON.parse(response);
                        amount = kms*res[0].amount;
                        amountContainer.val(amount.toFixed(0));
                        totalAmount = parseInt(totalAmount)+ parseInt(amount);
                        console.log(totalAmount);
                        totalAmountContainer.val(totalAmount.toFixed(0));
                    }
                });
            }
        }).on('change', "select[name^='mode_of_travel_']",function(){
            var category = $('#'+$(this).closest('div.card').data('parent')+' option:selected').text();
            if(category == 'Fuel - Mileage (within city)' || category == 'Fuel - Mileage (out of city)'){
                var totalAmountContainer = $('#total_'+$(this).closest('div.card').data('parent'));
                var kmsContainer = $(this).closest('div.form-row').find("input[name^='kms_']");
                var amountContainer = $(this).closest('div.form-row').find("input[name^='amount_']");
                var totalAmount = totalAmountContainer.val()-amountContainer.val();
                kmsContainer.val(0);
                amountContainer.val(0);
                totalAmount = parseInt(totalAmount);
                totalAmountContainer.val(totalAmount.toFixed(0));
            }
        }).on('change', "input[name^='rate_per_litre_']", function(){
            var amountPaid = $(this).closest('div.form-row').find("input[name^='amount_paid_']").val();
            var ratePerLitre = $(this).val();
            var litres = amountPaid/ratePerLitre;
            $(this).closest('div.form-row').find("input[name^='litres_']").val(litres.toFixed(2))
        }).on('change', "input[name^='amount_paid_']", function(){
            var category = $('#'+$(this).closest('div.card').data('parent')+' option:selected').text();
            if(category == 'Fuel - Receipts' || category == 'Local - Transport'){
                var totalAmountContainer = $('#total_'+$(this).closest('div.card').data('parent'));
                var amountContainer = $(this).closest('div.form-row').find("input[name^='amount_']");
                var amountPaidContainer = $(this).closest('div.form-row').find("input[name^='amount_paid_']");

                var totalAmount = totalAmountContainer.val()-amountContainer.val();
                var amount = amountPaidContainer.val();
                amountContainer.val(parseInt(amount).toFixed(0));
                totalAmount = parseInt(totalAmount)+ parseInt(amount);
                totalAmountContainer.val(totalAmount.toFixed(0));

                if(category == 'Fuel - Receipts'){
                    var ratePerLitre = $(this).closest('div.form-row').find("input[name^='rate_per_litre_']").val();
                    var amountPaid = $(this).val();
                    if(ratePerLitre > 0){
                        var litres = amountPaid/ratePerLitre;
                        $(this).closest('div.form-row').find("input[name^='litres_']").val(litres)
                    }
                }
            }
            else if(category == 'Meal - Entertainment' || category == 'Local - Toll tax / Parking charges / E-Tag' || category == 'Misc' || category == 'Local - Toll tax / Parking charges / E-Tag (out of city)' || category == 'Mobile - Card / PTCL' || category == 'Medicines - Mother'){
                var totalAmountContainer = $('#total_'+$(this).closest('div.card').data('parent'));
                var amountContainer = $(this).closest('div.form-row').find("input[name^='amount_']");
                var amountPaidContainer = $(this).closest('div.form-row').find("input[name^='amount_paid_']");
                var totalAmount = totalAmountContainer.val()-amountContainer.val();
                var amount = amountPaidContainer.val();

                amountContainer.val(parseInt(amount).toFixed(0));
                totalAmount = parseInt(totalAmount)+ parseInt(amount);
                totalAmountContainer.val(totalAmount.toFixed(0));
            }

            else if(category == 'Local - Hotel Stay'){
                var amountToBePaidContainer = $(this).closest('div.form-row').find("input[name^='amount_to_be_paid_']");
                var tripDuration = ($(this).closest('div.form-row').find("input[name^='date_range_']").val()).split("-");
                var amountPaidContainer = $(this);
                var amountContainer = $(this).closest('div.form-row').find("input[name^='amount_']");
                var totalAmountContainer = $('#total_'+$(this).closest('div.card').data('parent'));
                var diffDays = $(this).closest('div.form-row').find("input[name^='duration_']").val();
                var from = $.format.date(new Date(tripDuration[0].split(' ')[0]), "yyyy-MM-dd");

                var totalAmount = totalAmountContainer.val()-amountContainer.val();
                var amount = $(this).val();
                var allowedAmount = 0;

                $.ajax({
                    url: "{{URL::to('get/rate')}}/" + category + "/" + from.trim(),
                    success:function(response){
                        var res = JSON.parse(response);
                        if(res[0].amount == -1){
                            amountContainer.val(amount);
                            amountToBePaidContainer.val(amount);
                            totalAmount = parseInt(totalAmount)+ parseInt(amount);
                            totalAmountContainer.val(totalAmount);
                            amountPaidContainer.val(amount);
                        }
                        else{
                            allowedAmount = diffDays*res[0].amount;
                            if(allowedAmount <= amount){
                                amountContainer.val(allowedAmount.toFixed(0));
                                amountToBePaidContainer.val(allowedAmount.toFixed(0));
                                totalAmount = parseInt(totalAmount)+ parseInt(allowedAmount);
                                totalAmountContainer.val(totalAmount);
                                amountPaidContainer.val(amount);
                            }
                            else{
                                amountContainer.val(amount);
                                amountToBePaidContainer.val(amount);
                                totalAmount = parseInt(totalAmount)+ parseInt(amount);
                                totalAmountContainer.val(totalAmount);
                                amountPaidContainer.val(amount);
                            }
                        }
                    }
                })
            }
            else if(category == 'Mobile - Handset'){
                var amountToBePaidContainer = $(this).closest('div.form-row').find("input[name^='amount_to_be_paid_']");
                var amountPaidContainer = $(this);
                var amountContainer = $(this).closest('div.form-row').find("input[name^='amount_']");
                var totalAmountContainer = $('#total_'+$(this).closest('div.card').data('parent'));
                var date = $(this).closest('div.form-row').find("input[name^='date_']").val();
                var from = $.format.date(new Date(), "yyyy-MM-dd");
                var totalAmount = totalAmountContainer.val()-amountContainer.val();
                var amount = $(this).val();
                var allowedAmount = 0;
                $.ajax({
                    url: "{{URL::to('get/rate')}}/" + category + "/" + date,
                    success:function(response){
                        var res = JSON.parse(response);
                        if(res[0].amount == -1){
                            amountContainer.val(amount);
                            amountToBePaidContainer.val(amount);
                            totalAmount = parseInt(totalAmount)+ parseInt(amount);
                            totalAmountContainer.val(totalAmount);
                            amountPaidContainer.val(amount);
                        }
                        else{
                            allowedAmount = res[0].amount;
                            if(allowedAmount <= amount){
                                amountContainer.val(allowedAmount.toFixed(0));
                                amountToBePaidContainer.val(allowedAmount.toFixed(0));
                                totalAmount = parseInt(totalAmount)+ parseInt(allowedAmount);
                                totalAmountContainer.val(totalAmount);
                                amountPaidContainer.val(amount);
                            }
                            else{
                                amountContainer.val(amount);
                                amountToBePaidContainer.val(amount);
                                totalAmount = parseInt(totalAmount)+ parseInt(amount);
                                totalAmountContainer.val(totalAmount);
                                amountPaidContainer.val(amount);
                            }
                        }
                    }
                })
            }
        }).on('focusout', "input[name^='kms_']", function(){
            var category = $('#'+$(this).closest('div.card').data('parent')+' option:selected').text();
            var totalAmountContainer = $('#total_'+$(this).closest('div.card').data('parent'));
            console.log(category);
            if(category == 'Local - DA'){
                var kms = $(this).val();
                if (kms != ''){
                    var daDays = 0;
                    var amount = 0;
                    if(kms >= 100){
                        var tripDuration = ($(this).closest('div.form-row').find("input[name^='date_range_']").val()).split("-");
                        var amountContainer = $(this).closest('div.form-row').find("input[name^='amount_']");
                        var eligiblePeriodContainer = $(this).closest('div.form-row').find("input[name^='da_eligible_period_']");
                        var daFrom = tripDuration[0].trim();
                        var daTo = tripDuration[1].trim();
                        var from = $.format.date(new Date(tripDuration[0].split(' ')[0]), "yyyy-MM-dd");
                        $.ajax({
                            url: "{{URL::to('calculate/days')}}",
                            method: "POST",
                            data: {
                                daFrom: daFrom,
                                daTo: daTo
                            },
                            success: function(response){
                                daDays = JSON.parse(response);
                                var totalAmount = totalAmountContainer.val()-amountContainer.val();
                                eligiblePeriodContainer.val(daDays);
                                if(daDays >= 0){
                                    $.ajax({
                                        url:"{{URL::to('get/rate')}}/" + category + "/" + from,
                                        success:function(response){
                                            var res = JSON.parse(response);
                                            amount = daDays*res[0].amount;
                                            amountContainer.val(amount);
                                            totalAmount = parseInt(totalAmount)+ parseInt(amount);
                                            totalAmountContainer.val(totalAmount);
                                        }
                                    });
                                }
                            }
                        });
                    }
                    else{
                        alert('D.A not allowed. Minimum distance eligible for D.A is 100 Kms round trip');
                        $(this).closest('div.form-row').find("input[name^='da_eligible_period_']").val('');
                        $(this).closest('div.form-row').find("input[name^='amount_']").val('');
                    }
                }
            }
        }).on('change', "select[name^=accomodation_food_], select[name^=currency_]", function(){
            var category = $('#'+$(this).closest('div.card').data('parent')+' option:selected').text();
            if(category == 'Foreign - DA'){
                var amount = 0;
                var accomodation_food = $(this).val();
                var eligiblePeriodContainer = $(this).closest('div.form-row').find("input[name^='da_eligible_period_']");
                var amountContainer = $(this).closest('div.form-row').find("input[name^='amount_']");
                var currencyContainer = $(this).closest('div.form-row').find("select[name^='currency_']");
                var forexAmountContainer = $(this).closest('div.form-row').find("select[name^='currency_']");
                var localAmountContainer = $(this).closest('div.form-row').find("select[name^='local_amount_']");
                if(accomodation_food){
                    var tripDuration = ($(this).closest('div.form-row').find("input[name^='date_range_']").val()).split("-");
                    var personalDays = $(this).closest('div.form-row').find("input[name^='personal_days_']").val();
                    var diffDays = 0;
                    var daFrom = tripDuration[0].trim();
                    var daTo = tripDuration[1].trim();
                    var from = $.format.date(new Date(tripDuration[0].split(' ')[0]), "yyyy-MM-dd");
                    $.ajax({
                        url: "{{URL::to('calculate/days')}}",
                        method: "POST",
                        data: {
                            daFrom: daFrom,
                            daTo: daTo
                        },
                        success: function(response){
                            console.log('Response: '+response);
                            diffDays = JSON.parse(response);
                            foreignRateCalculation();
                        }
                    });
                    function foreignRateCalculation(){
                        if (personalDays < diffDays){
                            diffDays = diffDays - personalDays;
                            if(accomodation_food == 'Own Accomodation'){
                                eligiblePeriodContainer.val((diffDays).toFixed(1));
                            }
                            else if(accomodation_food == 'Accomodation by Host'){
                                eligiblePeriodContainer.val((diffDays/2).toFixed(1));
                            }
                            else if(accomodation_food == 'Both Accomodation & Food by Host'){
                                eligiblePeriodContainer.val((diffDays/3).toFixed(1));
                            }
                            var eligible_period = eligiblePeriodContainer.val();
                            $.ajax({
                                url: "{{URL::to('get/rate')}}/" + category + "/" + from,
                                success:function(response){
                                    var res = JSON.parse(response);
                                    amount = eligible_period*res[0].amount;
                                    amountContainer.val(amount);
                                }
                            })
                        }
                        else{
                            alert('Personal days must be less than DA days');
                            $(this).closest('div.form-row').find("input[name^='personal_days_']").val(0)
                        }
                    }
                }
                else{
                    eligiblePeriodContainer.val(0);
                    amountContainer.val(0);
                }
            }
        }).on('change', "input[name^=date_range_]", function(){
            var category = $('#'+$(this).closest('div.card').data('parent')+' option:selected').text();
            if(category == 'Local - Hotel Stay'){
                var durationContainer = $(this).closest('div.form-row').find("input[name^='duration_']");

                var tripDuration = ($(this).closest('div.form-row').find("input[name^='date_range_']").val()).split("-");
                var dateFrom = new Date((tripDuration[0]).trim());
                var dateTo = new Date((tripDuration[1]).trim());
                var timeDiff = Math.abs(dateTo.getTime() - dateFrom.getTime());
                var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
                diffDays = diffDays+1;
                durationContainer.val(diffDays);
            }
        }).on('change', "input[name^=forex_amount_]", function(){
            var category = $('#'+$(this).closest('div.card').data('parent')+' option:selected').text();
            if(category == 'Foreign - Expenses'){
                var amountContainer = $(this).closest('div.form-row').find("input[name^='amount_']");

                var totalAmountContainer = $('#total_'+$(this).closest('div.card').data('parent'));
                var totalAmount = totalAmountContainer.val()-amountContainer.val();
                var amount = $(this).val();
                amountContainer.val(amount);
                totalAmount = parseInt(totalAmount)+ parseInt(amount);
                totalAmountContainer.val(totalAmount);
            }
        }).on('change', "select[name^=description_]", function(){
            var category = $('#'+$(this).closest('div.card').data('parent')+' option:selected').text();
            if(category == 'Meal - Out of office' || category == 'Meal - Out of Lahore Posting'){
                var amountContainer = $(this).closest('div.form-row').find("input[name^='amount_']");
                var meal_dates = $(this).closest('div.form-row').find("input[name^='meal_dates_']");
                var description = $(this).closest('div.form-row').find("select[name^='description_']").val();
                var totalAmountContainer = $('#total_'+$(this).closest('div.card').data('parent'));
                var amount = 0;
                var mealDays = meal_dates.val().split(',');
                mealDays = mealDays.length;
                var from = $(this).closest('div.form-row').find("select[name^='date_']").val();
                var totalAmount = totalAmountContainer.val()-amountContainer.val();
                $.ajax({
                    url: "{{URL::to('get/rate')}}/" + description + "/" + from,
                    success:function(response){
                        var res = JSON.parse(response);
                        amount = res[0].amount*mealDays;
                        amountContainer.val(amount);
                        totalAmount = parseInt(totalAmount)+ parseInt(amount);
                        totalAmountContainer.val(totalAmount.toFixed(0));
                    },
                    error:function(response){
                        console.log(response);
                    }
                });

            }
        }).on('click', '#removeCategory', function(){
            if(confirm('Are you sure you want to remove this ?')){
                var categoryContainer = $(this).closest('div.form-row');
                var categoryItemsContainer = $(this).closest('div.form-row').find("select[name^='voucher_categories_'] option:selected");
                $('#'+categoryItemsContainer.data('view')).remove();
                categoryContainer.remove();
            }

        }).on('click', '#removeCategoryItem', function(){
            if(confirm('Are you sure you want to remove this ?')){
                var categoryItemContainer = $(this).closest('div.form-row');
                var totalAmountContainer = $('#total_'+$(this).closest('div.card').data('parent'));
                var amountContainer = $(this).closest('div.form-row').find("input[name^='amount_']");

                var totalAmount = totalAmountContainer.val() - amountContainer.val();
                totalAmountContainer.val(totalAmount);
                categoryItemContainer.remove();
            }
        }).on('change', '.datetimeOutOfLahore', function(){
            var duration = ($(this).closest('div.form-row').find("input[name^='date_range_']").val()).split("-");
            var dateFrom = new Date((duration[0]).trim());
            var dateTo = new Date((duration[1]).trim());

            var timeDiff = Math.abs(dateTo.getTime() - dateFrom.getTime());
            var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
        }).on('change', '.official_travel', function(){
            if($(this).val() == 'Yes'){
                if(confirm('Are you sure that you were on official travel other than Kasur')){
                    $(this).val('Yes');
                }
                else{
                    $(this).val('No');
                }
            }
        });
    </script>
    <script>
        $('#chargeTo').on('change', function(){
            if($(this).val() == 'Cost Center'){
                $('#costCenter').removeAttr('hidden');
                $('.costCenter').attr('required', true);
                $('.costCenter').addClass('select2');
                $('.select2').select2();
                $('#orderNumber').attr('hidden', true);
                $('#orderNumber').removeAttr('required');
            }
            else if($(this).val() == 'Order'){
                $('.costCenter').removeAttr('required');
                $('.costCenter').next('.select2-container').hide();
                $('#costCenter').attr('hidden', true);
                $('#orderNumber').removeAttr('hidden');
                $('#orderNumber').attr('required', true);
            }
            else{
                $('.costCenter').removeAttr('required');
                $('.costCenter').next('.select2-container').hide();
                $('#costCenter').attr('hidden', true);
                $('#orderNumber').removeAttr('hidden');
                $('#orderNumber').attr('hidden', true);
            }
        });
        $('#addCategory').click(function(evt){
            evt.preventDefault();
            if($('#voucher_categories_'+categoriesCount).val() == ""){
                alert('You cannot add more categories without completing the first category');
            }
            else{
                categoriesCount = categoriesCount+1;
                $('#categoriesCount').val(categoriesCount);
                $.ajax({
                    url: "{{ URL::to('voucher/category/add') }}/" + categoriesCount,
                    success:function (response){
                        $("#rowContainer").append(JSON.parse(response).row);
                        $('.select2').select2({
                            tags: true
                        });
                    }
                });
            }
        });
        function localRateCalculation(from){
            $.ajax({
                url: "{{URL::to('calculate/forex-to-local')}}" + "/" + from,
                success:function(response){
                    var ress = JSON.parse(response);
                }
            });

        }
    </script>
    @if(isset($voucher))
        <script>
            $(document).ready(function(){
                $('.card-content').addClass('show');
                $('.select2').select2({
                    tags: true
                });
            })
        </script>
    @endif
    <script>
        $('#voucher-form').on('keypress', function(e){
            var key = e.charCode || e.keyCode || 0;
            if(key==13){
                e.preventDefault();
            }
        })
    </script>
@endsection