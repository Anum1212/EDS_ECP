@extends('layouts.ers-layout')

@section('header')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/custom/dashboard.css') }}">
    <style>
        .header-image {
            background: url('{{ asset('assets/img/good-afternoon.jpeg') }}') no-repeat center center;
            background-size: cover;
        }
    </style>
@endsection

@section('body')
    <div class="header-image">
        <div class="header-text" id="greeting"></div>
    </div>
        <div class="row col-12 mt-3">
            <!-- First Row: Create New Section -->
            <h2 class="quick-actions-heading">Quick Actions</h2>
        </div>
        <div class="row col-12">
            <a href="{{ URL::to('voucher/add') }}">
                <div class="quick-action-tile">
                    <i class="la la-file-text tile-icon"></i>
                    <div class="quick-tile-text">New Claim</div>
                </div>
            </a>
            <a href="{{ URL::to('voucher/medicines-mother-claim') }}"
               >
                <div class="quick-action-tile">
                    <i class="la la-medkit tile-icon"></i>
                    <div class="quick-tile-text">Medicines Mother</div>
                </div>
            </a>
            <a href="{{ URL::to('fuel-claim/add') }}">
                <div class="quick-action-tile">
                    <i class="fa fa-gas-pump tile-icon"></i>
                    <div class="quick-tile-text">Fuel Reimbursement</div>
                </div>
            </a>
            <a href="{{ URL::to('fuel-mileage/add') }}">
                <div class="quick-action-tile">
                    <i class="fa fa-gas-pump tile-icon"></i>
                    <div class="quick-tile-text">Fuel Mileage</div>
                </div>
            </a>
            <a href="{{ URL::to('travel-claim/add') }}">
                <div class="quick-action-tile">
                    <i class="la la-plane tile-icon"></i>
                    <div class="quick-tile-text">New Travel Claim</div>
                </div>
            </a>
            @if ($employee->department->businessUnit->company->id != 1700 &&
                    $employee->department->businessUnit->company->id != 1100 &&
                    $employee->department->businessUnit->company->id != 1300 &&
                    $employee->department->businessUnit->company->id != 1500 &&
                    $employee->department->businessUnit->company->id != 1200 &&
                    $employee->department->businessUnit->company->id != 1000)
                <a href="{{ URL::to('medical-claim/add') }}">
                    <div class="quick-action-tile">
                        <i class="fa fa-briefcase-medical tile-icon"></i>
                        <div class="quick-tile-text">Medical Claim</div>
                    </div>
                </a>
            @endif
            <a href="{{ URL::to('travel-order/add') }}">
                <div class="quick-action-tile">
                    <i class="la la-plane tile-icon"></i>
                    <div class="quick-tile-text">New Travel Order</div>
                </div>
            </a>
            
            {{-- Y-Lunch Section --}}
            @if($employee->entitlements->contains('entitlement_type', 'Y-Lunch'))
            <a href="{{ URL::to('mess-booking/add') }}">
                <div class="quick-action-tile">
                    <i class="la la-plane tile-icon"></i>
                    <div class="quick-tile-text">New Y-Lunch</div>
                </div>
            </a>
            @endif
        </div>
        <div class="col-12 mt-3">
            <h2 class="quick-actions-heading">Reimbursement Status</h2>
        </div>
        <div class="row col-12">
            <a href="{{ URL::to('vouchers') }}" class="status-tile-link">
                <div class="status-action-tile progress-tile">
                    <i class="la la-check-circle status-tile-icon"></i>
                    <div class="tile-text">
                        <span class="counter">{{ count($employee->vouchers) }}</span>
                    </div>
                    <span class="tile-title">Total Claims</span>
                    <div class="progress-bar">
                        <div class="progress" style="width: {{ count($employee->vouchers) == 0 ? 0 : 100 }}%;"></div>
                    </div>
                </div>
            </a>
            <a href="{{ URL::to('vouchers/approved') }}" class="status-tile-link">
                <div class="status-action-tile progress-tile">
                    <i class="la la-check-circle status-tile-icon"></i>
                    <div class="tile-text">
                        <span class="counter">{{ count($employee->approvedVouchers) }}</span>
                    </div>
                    <span class="tile-title">Approved Claims</span>
                    <div class="progress-bar">
                        <div class="progress"
                            style="width: {{ count($employee->vouchers) == 0 ? 0 : (count($employee->approvedVouchers) / count($employee->vouchers)) * 100 }}%;">
                        </div>
                    </div>
                </div>
            </a>
            <a href="{{ URL::to('vouchers/unapproved') }}" class="status-tile-link">
                <div class="status-action-tile progress-tile">
                    <i class="la la-times-circle status-tile-icon"></i>
                    <div class="tile-text">
                        <span class="counter">{{ count($employee->unapprovedVouchers) }}</span>
                    </div>
                    <span class="tile-title">Unapproved Claims</span>
                    <div class="progress-bar">
                        <div class="progress"
                            style="width: {{ count($employee->vouchers) == 0 ? 0 : (count($employee->unapprovedVouchers) / count($employee->vouchers)) * 100 }}%;">
                        </div>
                    </div>
                </div>
            </a>
            <a href="{{ URL::to('travel-orders') }}" class="status-tile-link">
                <div class="status-action-tile progress-tile">
                    <i class="la la-check-square status-tile-icon"></i>
                    <div class="tile-text">
                        <span class="counter">{{ count($employee->travelOrders) }}</span>
                    </div>
                    <span class="tile-title">Total Travel Orders</span>
                    <div class="progress-bar">
                        <div class="progress" style="width: {{ count($employee->travelOrders) == 0 ? 0 : 100 }}%;"></div>
                    </div>
                </div>
            </a>
            <a href="{{ URL::to('travel-orders/approved') }}" class="status-tile-link">
                <div class="status-action-tile progress-tile">
                    <i class="la la-check-square status-tile-icon"></i>
                    <div class="tile-text">
                        <span class="counter">{{ count($employee->approvedTravelOrders) }}</span>
                    </div>
                    <span class="tile-title">Approved Travel Orders</span>
                    <div class="progress-bar">
                        <div class="progress"
                            style="width: {{ count($employee->travelOrders) == 0 ? 0 : (count($employee->approvedTravelOrders) / count($employee->travelOrders)) * 100 }}%;">
                        </div>
                    </div>
                </div>
            </a>
            <a href="{{ URL::to('travel-orders/unapproved') }}" class="status-tile-link">
                <div class="status-action-tile progress-tile">
                    <i class="la la-exclamation-circle status-tile-icon"></i>
                    <div class="tile-text">
                        <span class="counter">{{ count($employee->unapprovedTravelOrders) }}</span>
                    </div>
                    <span class="tile-title">Unapproved Travel Orders</span>
                    <div class="progress-bar">
                        <div class="progress"
                            style="width: {{ count($employee->travelOrders) == 0 ? 0 : (count($employee->unapprovedTravelOrders) / count($employee->travelOrders)) * 100 }}%;">
                        </div>
                    </div>
                </div>
            </a>

            <a href="{{ URL::to('mess-bookings/') }}" class="status-tile-link">
                <div class="status-action-tile progress-tile">
                    <i class="la la-exclamation-circle status-tile-icon"></i>
                    <div class="tile-text">
                        <span class="counter">{{ count($employee->totalYLunch) }}</span>
                    </div>
                    <span class="tile-title">Total Y_Lunch</span>
                    <div class="progress-bar">
                        <div class="progress"
                            style="width: {{ count($employee->totalYLunch) == 0 ? 0 : 100 }}%;">
                        </div>
                    </div>
                </div>
            </a><a href="{{ URL::to('mess-bookings/approved') }}" class="status-tile-link">
                <div class="status-action-tile progress-tile">
                    <i class="la la-exclamation-circle status-tile-icon"></i>
                    <div class="tile-text">
                        <span class="counter">{{ count($employee->approvedYLunch) }}</span>
                    </div>
                    <span class="tile-title">Approved Y-Lunch</span>
                    <div class="progress-bar">
                        <div class="progress"
                            style="width: {{ count($employee->totalYLunch) == 0 ? 0 : (count($employee->approvedYLunch) / count($employee->totalYLunch)) * 100 }}%;">
                        </div>
                    </div>
                </div>
            </a><a href="{{ URL::to('mess-bookings/unapproved') }}" class="status-tile-link">
                <div class="status-action-tile progress-tile">
                    <i class="la la-exclamation-circle status-tile-icon"></i>
                    <div class="tile-text">
                        <span class="counter">{{ count($employee->unapprovedYLunch) }}</span>
                    </div>
                    <span class="tile-title">Unapproved Y-Lunch</span>
                    <div class="progress-bar">
                        <div class="progress"
                            style="width: {{ count($employee->totalYLunch) == 0 ? 0 : (count($employee->unapprovedYLunch) / count($employee->totalYLunch)) * 100 }}%;">
                        </div>
                    </div>
                </div>
            </a>
        </div>
    <script>
        function updateGreeting() {
            const hours = new Date().getHours();
            const greetingText = document.getElementById('greeting');

            if (hours < 12) {
                greetingText.textContent = 'Good morning!';
            } else if (hours < 18) {
                greetingText.textContent = 'Good afternoon!';
            } else {
                greetingText.textContent = 'Good evening!';
            }
        }

        updateGreeting();
    </script>
@endsection
