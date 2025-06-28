@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/daterange/daterangepicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/custom/daterangepicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/custom/air-date-picker/css/datepicker.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/animate/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/custom/vouchers.css') }}">
    <style>
        input[type='number'] {
            -moz-appearance: textfield;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
        }

        .yLunch label {
            font-weight: bold;
            padding-top: 10px
        }
    </style>
@endsection

@section('body')
<div class="content-body">
    <section id="form-control-repeater">
            <form action="{{ URL::to($storingURL) }}" method="post" id="mess-booking-form" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="row justify-content-center">
                <div class="col-md-11">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title font-weight-bold" id="file-repeater">Your Profile</h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-h font-medium-3"></i></a>
                        </div>
                        <div class="card-content collapse show">
                            <div class="card-body">
                                <div class="row">
                                    <!-- User image on the right -->
                                    <div class="col-md-3 order-md-2 text-center">
                                        @if ($photo != null && $photo_mimetype != null)
                                            <img src="data:{{ $photo_mimetype }};base64,{{ base64_encode($photo) }}"
                                                alt="Profile Image" class="img-fluid rounded-circle w-50">
                                        @else
                                            <img src="{{ asset('assets/img/user-placeholder-removebg.png') }}"
                                                alt="Profile Image" class="img-fluid rounded-circle w-50">
                                        @endif
                                        <p class="mt-2"><strong>{{ $employee->employee_name }}</strong></p>
                                    </div>
                                        <!-- Profile details -->
                                        <div class="col-md-9 order-md-1 w-100 overflow-auto">
                                        <table class="table">
                                            <tbody>
                                                    <tr>
                                                        <td class="text-right"><strong>Employee ID: </strong></td>
                                                        <td>{{ $employee_sf_data->employee_number }}</td>
                                                        <td class="text-right"><strong>Name: </strong></td>
                                                        <td>{{ $employee->employee_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right"><strong>Grade: </strong></td>
                                                        <td>{{ $employee_sf_data->grade }}</td>
                                                        <td class="text-right"><strong>Designation: </strong></td>
                                                        <td>{{ $employee_sf_data->designation }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right"><strong>Business Unit: </strong></td>
                                                        <td>{{ $employee->department->BusinessUnit->bu_name }}</td>
                                                        <td class="text-right"><strong>Department: </strong></td>
                                                        <td>{{ $employee->department->department_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right"><strong>Company: </strong></td>
                                                        <td>{{ $employee->department->BusinessUnit->company->company_name }}
                                                        </td>
                                                        <td class="text-right"><strong>Cost Center: </strong></td>
                                                        <td>{{ $employee_sf_data->cost_center }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right"><strong>Line Manager: </strong></td>
                                                        <td>{{ $employee_sf_data->line_manager->employee->employee_name }}
                                                        </td>
                                                        <td class="text-right"><strong>Business Unit Approver: </strong>
                                                        </td>
                                                        <td>{{ $employee_sf_data->BusinessUnit->bu_head->employee->employee_name }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                        </table>
                                    </div>
                                </div>
                            
                                    <!-- Enter Details Section -->
                                    <div class="col-md-12">
                                        <hr>
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title" id="file-repeater" style="font-weight: bold;">Enter
                                                    Details</h4>
                                                <a class="heading-elements-toggle"><i
                                                        class="la la-ellipsis-h font-medium-3"></i></a>
                                                <div class="heading-elements">
                                                    <ul class="list-inline mb-0">
                                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="card-content collapse show">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="row col-md-5 form-group yLunch">
                                                            <div class="col-md-4">
                                                                <label for="Title">Description:</label>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="Title"
                                                                    name="booking_name" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1"></div>
                                                        <div class="row col-md-5 form-group yLunch">
                                                            <div class="col-md-4">
                                                                <label for="TotalHeadCount">Type:</label>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <select name="FoodType" id="FoodType"
                                                                    class="form-control custom-input">
                                                                    <option value="" disabled selected>Select Type
                                                                    </option>
                                                                    @foreach ($messBookingTypes as $messBookingType)
                                                                        <option value="{{ $messBookingType->id }}">
                                                                            {{ $messBookingType->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row col-md-4 form-group yLunch">
                                                            <div class="col-md-6">
                                                                <label for="BSPEmployeeHeadCount">BSP Employee Head
                                                                    Count:</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number" class="form-control"
                                                                    name="BSPEmployeeHeadCount" id="BSPEmployeeHeadCount"
                                                                    onchange="totalHeadCount()">
                                                            </div>
                                                        </div>
                                                        <div class="row col-md-4 form-group yLunch">
                                                            <div class="col-md-6">
                                                                <label for="GuestHeadCount">Guest Head Count:</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number" class="form-control"
                                                                    name="GuestHeadCount" id="GuestHeadCount"
                                                                    onchange="totalHeadCount()">
                                                            </div>
                                                        </div>
                                                        <div class="row col-md-4 form-group yLunch">
                                                            <div class="col-md-6">
                                                                <label for="TotalHeadCount">Total Head Count:</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number" class="form-control"
                                                                    name="TotalHeadCount" id="TotalHeadCount" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="row col-md-4 form-group yLunch">
                                                            <div class="col-md-6">
                                                                <label for="BookingDateStart">Booking Date (Start):</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text"
                                                                    class="form-control custom-input date-range-picker"
                                                                    name="BookingDateStart" id="BookingDateStart"
                                                                    value="" onchange="setMinEndDate()" required>
                                                            </div>
                                                        </div>
                                                        <div class="row col-md-4 form-group yLunch">
                                                            <div class="col-md-6">
                                                                <label for="BookingDateEnd">Booking Date (End):</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text"
                                                                    class="form-control custom-input date-range-picker"
                                                                    name="BookingDateEnd" id="BookingDateEnd"
                                                                    value="" required>
                                                            </div>
                                                        </div>
                                                        <div class="row col-md-4 form-group yLunch">
                                                            <div class="col-md-6">
                                                                <label for="BookingTime">Booking Time:</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control custom-input"
                                                                    name="BookingTime" id="BookingTime" value=""
                                                                    required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Button to Submit Form -->
                                        <div class="form-group col-md-12 mt-3">
                                            <button type="submit" class="btn custom-button">Submit</button>
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

<style>
    .label-bar {
        display: flex;
        align-items: center;
        background-color: #f0f0f0;
        padding: 8px;
        margin-bottom: 8px;
        border-radius: 4px;
    }

    .label-bar .label {
        padding: 0 8px;
        font-weight: bold;
        text-align: left;
    }

    .required {
        color: red;
        font-weight: bold;
        margin-right: 4px;
    }

    .custom-input:hover,
    .select-custom:hover {
        box-shadow: 0 0 4px rgba(0, 0, 0, 0.7);
    }

    .custom-button {
        background-color: transparent;
        border: none;
        color: inherit;
        padding: 5px 10px;
    }

    .light-blue-icon {
        color: #03A2DD;
    }

    .btn-light {
        background-color: #d3d3d3;
        border: none;
    }

    .btn-light.btn-sm {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
    }

    .btn-light:hover {
        background-color: #d4d4d4;
    }

    .form-control,
    .custom-input {
        /* height: 35%; */
    }

    .custom-button {
        background-color: #03A2DD;
        /* Button background color */
        border: 2px solid #03A2DD;
        /* Border color matching the button */
        border-radius: 8px;
        /* Adjust the border radius as needed */
        color: white;
        /* Text color */
        padding: 10px 20px;
        /* Padding for better click area */
        cursor: pointer;
        /* Pointer cursor on hover */
        transition: background-color 0.3s, border-color 0.3s;
        /* Transition effect */
    }

    .custom-button:hover {
        background-color: white;
        /* Change background on hover */
        color: #03A2DD;
        /* Change text color on hover */
        border-color: #03A2DD;
        /* Maintain border color on hover */
    }

    .attachment-icon {
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }

    .attachment-label {
        color: #03A2DD;
        font-size: 24px;
        /* Adjust size as needed */
        cursor: pointer;
    }

    .attachment-label:hover {
        color: #03A2DD;
    }

    .attachment-input {
        display: none;
        /* Hide the file input */
    }
</style>

@section('footer')
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.time.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/legacy.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/dateTime/moment-with-locales.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/pickers/daterange/daterangepicker.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/js/scripts/custom/daterangepicker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/date.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/air-date-picker/js/datepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/js/scripts/custom/timepicker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/js/scripts/custom/dayjs.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/air-date-picker/js/i18n/datepicker.en.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/js/scripts/modal/components-modal.js') }}" type="text/javascript"></script>

    <script>
        function totalHeadCount() {
            var bspHeadCount = $('#BSPEmployeeHeadCount').val() || 0;
            var guestHeadCount = $('#GuestHeadCount').val() || 0;
            var totalHeadCount = parseInt(bspHeadCount) + parseInt(guestHeadCount);
            $('#TotalHeadCount').val(totalHeadCount);
        }

        var startDate = '';
        $('#BookingDateStart').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: {
                format: 'YYYY-MM-DD' // Display format for date and time
            },
            autoUpdateInput: true,
        });

        function setMinEndDate() {
            startDate = $('#BookingDateStart').val();
            $('#BookingDateEnd').val(startDate);
        }

        $('#BookingDateEnd').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            "minDate": startDate,
            locale: {
                format: 'YYYY-MM-DD' // Display format for date and time
            },
            autoUpdateInput: true,
        });

        $('#BookingTime').timepicker({
            format: 'HH:mm',
        });

        $('#BookingTime').val(dayjs().format('HH:mm'));
    </script>
@endsection
