@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" ef="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" ef="{{ asset('app-assets/vendors/css/pickers/daterange/daterangepicker.css') }}">
    <link rel="stylesheet" type="text/css" ef="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.css') }}">
    <link rel="stylesheet" type="text/css" ef="{{ asset('app-assets/css/custom/daterangepicker.css') }}">
    <link rel="stylesheet" type="text/css" ef="{{ asset('assets/custom/air-date-picker/css/datepicker.min.css') }}">
    <link rel="stylesheet" type="text/css" ef="{{ asset('app-assets/css/plugins/animate/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/custom/vouchers.css')}}">
@endsection

@section('body')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-10 mb-2" style="color: white">
            <h3 class="content-header-title" style="color: white">{{ $panelHeading }}</h3>
        </div>
    </div>
    <div class="content-body">
        <section id="form-control-repeater">
            <form action="{{ URL::to($storingURL) }}" method="post" id="voucher-form" enctype="multipart/form-data" onsubmit="return validateForm()">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="itemCount" id="itemCount" value="{{ isset($voucher) ? $itemCount : 1 }}">
                <input type="hidden" name="claimType" id="claimType" value="medicines-mother">
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
                                                        <td class="text-right"><strong>Bank: </strong></td>
                                                        <td>{{ $employee_sf_data->bank }}</td>
                                                        <td class="text-right"><strong>Account Number: </strong></td>
                                                        <td>{{ $employee_sf_data->account_number }}</td>
                                                    </tr>

                                                    @if($employee->department->BusinessUnit->company->id == '1400' || $employee->department->BusinessUnit->company->id == '1700')
                                                    <tr>
                                                        <td class="text-right"><strong>Line Manager: </strong></td>
                                                        <td>{{ isset($employee_sf_data->line_manager->employee->employee_name) ? $employee_sf_data->line_manager->employee->employee_name : 'Not Found' }}
                                                        </td>
                                                        <td class="text-right"><strong>Business Unit Approver: </strong>
                                                        </td>
                                                        <td>{{ isset($employee_sf_data->BusinessUnit->bu_head->employee->employee_name) ? $employee_sf_data->BusinessUnit->bu_head->employee->employee_name : 'Not Found' }}
                                                        </td>
                                                    </tr>
                                                    @endif
                                                    @if (isset($entitlements))
                                                        <tr>
                                                            <td class="text-right"><strong>Total OPD Limit: </strong></td>
                                                            <td>{{ number_format($entitlements->total_limit) }} PKR</td>
                                                            <td class="text-right"><strong>Consumed OPD Limit </strong></td>
                                                            <td>{{ number_format($entitlements->consumed_limit) }} PKR</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-right"><strong>Remaining OPD Limit </strong>
                                                            </td>
                                                            <td>{{ number_format($entitlements->total_limit - $entitlements->consumed_limit) }}
                                                                PKR</td>
                                                            <td class="text-right"></td>
                                                            <td></td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Enter Details Section -->
                                    <div class="col-md-12">
                                            <div class="card-header">
                                                <h4 class="card-title font-weight-bold" id="file-repeater">Enter Details
                                                </h4>
                                                <a class="heading-elements-toggle"><i
                                                        class="la la-ellipsis-h font-medium-3"></i></a>
                                                <div class="heading-elements">
                                                    <ul class="list-inline mb-0">
                                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div id="rowContainer">
                                                <div id="category-form" class="row">
                                                    <!-- Label bar (only appears once, not cloned) -->
                                                    <div class="label-bar row col-12">
                                                        <div class="col label text-center">Category<span class="required">*</span></div>
                                                        <div class="col label text-center">Date<span class="required">*</span></div>
                                                        <div class="col label text-center">Amount Paid<span class="required">*</span>
                                                        </div>
                                                        <div class="col label text-center">Receipt Provided<span
                                                                class="required">*</span>
                                                            </div>
                                                        <div class="col-1 label"></div>
                                                        <div class="col-1 label"></div>
                                                    </div>

                                                    <div id="form-rows" class="row col-12">
                                                        @if (isset($voucher))
                                                            <div class="form-group col">
                                                                <select name="category_1" id="category_1"
                                                                    class="form-control custom-input">
                                                                    <option value="" style="height: 50%;" disabled selected>
                                                                        Select Category</option>
                                                                    @foreach ($categories as $category)
                                                                        @if ($category->enabled)
                                                                            <option value="{{ $category->id }}">
                                                                                {{ $category->category_name }}
                                                                            </option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group col">
                                                                <input type="date" class="form-control custom-input sameAsSelectHeight"
                                                                    name="date_1" id="date_1"
                                                                    min="{{ date('Y-m-d', strtotime(date('Y-m-d') . ' - 3 months')) }}"
                                                                    value="{{ date('Y-m-d', strtotime($item->date_from)) }}"
                                                                    required>
                                                            </div>
                                                            <div class="form-group col">
                                                                <input type="text" class="form-control custom-input"
                                                                    name="recipt_number_1" id="recipt_number_1">
                                                            </div>
                                                            <div class="form-group col">
                                                                <input type="text" class="form-control custom-input"
                                                                    name="description_1" id="description_1"
                                                                    value="{{ $item->description }}" required>
                                                            </div>
                                                            <div class="form-group col">
                                                                <input type="text" class="form-control custom-input"
                                                                    value="{{ $item->amount_paid }}" name="amount_1"
                                                                    id="amount_1" required>
                                                            </div>
                                                            <div class="form-group col">
                                                                <select class="form-control select-custom"
                                                                    name="receipt_provided_1" style="height: 50%;" required>
                                                                    <option
                                                                        {{ $item->receipt_copy == 1 ? 'selected' : '' }}
                                                                        value="1">Yes</option>
                                                                    <option
                                                                        {{ $item->receipt_copy == 0 ? 'selected' : '' }}
                                                                        value="0">No</option>
                                                                </select>
                                                            </div>
                                                            <!--<div class="form-group attachment-icon">-->
                                                            <!--    <label for="attachment_1" class="attachment-label">-->
                                                            <!--        <i class="la la-paperclip"></i>-->
                                                            <!--    </label>-->
                                                            <!--    <input type="file" id="attachment_1"-->
                                                            <!--        name="attachment_1" class="attachment-input col"-->
                                                            <!--        style="display: none;">-->
                                                            <!--</div>-->
                                                            <div class="form-group attachment-icon" style="margin-left: 2%; margin-bottom: 2%;">
                                                                            <label for="attachment_{{$itemCount}}" class="attachment-label" style="cursor: pointer;">
                                                                                <i class="la la-paperclip"></i>
                                                                            </label>
                                                                        
                                                                            <input
                                                                                type="file"
                                                                                id="attachment_{{$itemCount}}"
                                                                                name="attachment_{{$itemCount}}"
                                                                                class="attachment-input"
                                                                                style="display: none;"
                                                                                onchange="handleAttachmentChange(this)"
                                                                            >
                                                                        
                                                                            <span id="file-name-{{$itemCount}}" class="file-name" style="margin-left: 10px; font-style: italic; color: #555;"></span>
                                                                            <span id="file-status-{{$itemCount}}" class="file-status" style="margin-left: 5px;"></span>
                                                                        </div>
                                                            <div class="form-group">
                                                                <a class="btn custom-button remove-row"><i
                                                                        class="la la-trash-o light-blue-icon"></i></a>
                                                            </div>
                                                        @else
                                                            <div id="template-row" class="form-row row col-12">
                                                                <div class="form-group col">
                                                                    <select name="category_1" id="category_1"
                                                                        class="form-control custom-input" style="height: 50%;">
                                                                        <option value="" disabled selected>
                                                                            Select Category</option>
                                                                        <option value="{{ $categories->id }}">
                                                                            {{ $categories->category_name }}
                                                                        </option>

                                                                    </select>
                                                                </div>
                                                                <div class="form-group col">
                                                                    <input type="date"
                                                                        class="form-control sameAsSelectHeight custom-input" name="date_1"
                                                                        id="date_1" required>
                                                                </div>
                                                                <div class="form-group col">
                                                                    <input type="text"
                                                                        class="form-control custom-input sameAsSelectHeight" name="amount_1"
                                                                        id="amount_1" required>
                                                                </div>
                                                                <div class="form-group col">
                                                                    <select class="form-control select-custom"
                                                                        name="receipt_provided_1" style="height: 50%;" required>
                                                                        <option value="1">Yes</option>
                                                                        <option value="0">No</option>
                                                                    </select>
                                                                </div>
                                                                <!--<div class="form-group attachment-icon col-1">-->
                                                                <!--    <label for="attachment_1" class="attachment-label">-->
                                                                <!--        <i class="la la-paperclip"></i>-->
                                                                <!--    </label>-->
                                                                <!--    <input type="file" id="attachment_1"-->
                                                                <!--        name="attachment_1" class="attachment-input"-->
                                                                <!--        style="display: none;">-->
                                                                <!--</div>-->
                                                                
                                                                <div class="form-group attachment-icon" style="margin-left: 2%; margin-bottom: 2%;">
                                                                        <label for="attachment_1" class="attachment-label" style="cursor: pointer;">
                                                                            <i class="la la-paperclip"></i>
                                                                        </label>
                                                                    
                                                                        <input
                                                                            type="file"
                                                                            id="attachment_1"
                                                                            name="attachment_1"
                                                                            class="attachment-input"
                                                                            style="display: none;"
                                                                            onchange="handleAttachmentChange(this)"
                                                                        >
                                                                    
                                                                        <span id="file-name-1" class="file-name" style="margin-left: 10px; font-style: italic; color: #555;"></span>
                                                                        <span id="file-status-1" class="file-status" style="margin-left: 5px;"></span>
                                                                </div>
                                                                <div class="form-group col-1">
                                                                    <a class="btn remove-row"><i
                                                                            class="la la-trash-o light-blue-icon"></i></a>
                                                                </div>
                                                            </div>
                                                        @endif
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

    .custom-input,
    .select-custom {
        height: 35px;
        box-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
        transition: box-shadow 0.2s ease;
        border: 1px solid #ced4da;
        border-radius: 4px;
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
        height: 35%;
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
    <script src="{{ asset('assets/custom/air-date-picker/js/i18n/datepicker.en.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/js/scripts/modal/components-modal.js') }}" type="text/javascript"></script>

    </script>
@endsection

<style>

</style>

<script>
      const employeeCompanyName = "{{ $employee->department->BusinessUnit->company->company_name }}";
      function validateForm() {
        const fileInput = document.getElementById('attachment_1');
        if (employeeCompanyName === "Tripack Films Limited") {
    
            if (!fileInput.files.length) {
              $('#attachmentRequiredModal').modal('show');
              return false;
            }
        
            return true;
          }
      }
</script>
<script>
    function handleAttachmentChange(input) {
        const container = input.closest(".attachment-icon");
        if (!container) return;
        console.log(container);
        const fileNameSpan = container.querySelector(".file-name");
        const fileStatusSpan = container.querySelector(".file-status");
    
        if (input.files && input.files.length > 0) {
            fileNameSpan.innerText = input.files[0].name;
            fileStatusSpan.innerText = "✅";
            fileStatusSpan.style.color = "green";
        } else {
            fileNameSpan.innerText = "No file selected";
            fileStatusSpan.innerText = "";
        }
}
</script>

<div class="modal fade" id="attachmentRequiredModal" tabindex="-1" role="dialog" aria-labelledby="attachmentRequiredModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="attachmentRequiredModalLabel">Attachment Required</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Please upload the required attachment before submitting the form.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
