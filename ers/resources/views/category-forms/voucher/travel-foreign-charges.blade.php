<div class="card" id="{{$categoryName}}" data-parent="{{$parent}}">
    <div class="card-header card-head-inverse bg-secondary">
        <a data-action="collapse"><h4 class="card-title text-white">Foreign - Expenses <span
                        class="badge badge bg-white text-secondary badge-pill mr-2"></span></h4></a>
        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                <li><a data-action="collapse"><i class="ft-plus"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="card-content collapse" style="overflow-x: auto">
        <div class="card-body">
            <p class="danger"><strong>Please note if you choose to enter manual exchange rate then you will have to provide evidence in hard form to the accounts department</strong></p>
            <hr>
            @if(isset($voucher))
                @foreach($voucher->categoryItems($voucher->id,$categoryID) as $item)
                    {{--*/ $itemCount++ /*--}}
                    <div class="form-row">
                        <div class="form-group col-md-2 mb-2">
                            <label>Date</label>
                            <input type="date" class="form-control" name="date_{{$itemCount}}" id="date_{{$itemCount}}"
                                   min="{{date('Y-m-d', strtotime(date('Y-m-d').' - 3 months'))}}"
                                   value="{{date('Y-m-d', strtotime($item->date_from))}}" required>
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Description of expense</label>
                            <select class="select2 form-control" name="description_{{$itemCount}}" required>
                                <option {{$item->description == 'Taxi'?'selected':''}} value="Taxi">Taxi</option>
                                <option {{$item->description == 'Train'?'selected':''}} value="Train">Train</option>
                                <option {{$item->description == 'Air'?'selected':''}} value="Air">Air</option>
                                <option {{$item->description == 'Sim charges'?'selected':''}} value="Sim charges">Sim
                                    charges
                                </option>
                                <option {{$item->description == 'Other charges'?'selected':''}} value="Other charges">
                                    Other charges
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Currency</label>
                            <select class="select2 form-control" name="cur_{{$itemCount}}" required>
                                <option {{$item->cur == 'EUR'?'selected':''}} value="EUR">EUR</option>
                                <option {{$item->cur == 'USD'?'selected':''}} value="USD">USD</option>
                                <option {{$item->cur == 'CAD'?'selected':''}} value="CAD">CAD</option>
                                <option {{$item->cur == 'ZAR'?'selected':''}} value="ZAR">ZAR</option>
                                <option {{$item->cur == 'GBP'?'selected':''}} value="GBP">GBP</option>
                                <option {{$item->cur == 'AED'?'selected':''}} value="AED">Dirham</option>
                                <option {{$item->cur == 'CHF'?'selected':''}} value="CHF">Swiss Franc</option>
                                <option {{$item->cur == 'SEK'?'selected':''}} value="SEK">Swiss Krona</option>
                                <option {{$item->cur == 'SAR'?'selected':''}} value="SAR">Riyal</option>
                                <option {{$item->cur == 'TRY'?'selected':''}} value="TRY">Turkish Lira</option>
                                <option {{$item->cur == 'CNY'?'selected':''}} value="CNY">Chinese Yuan</option>
                                <option {{$item->cur == 'LKR'?'selected':''}} value="LKR">Sri Lankan Rupee (LKR)</option>
                                <option {{$item->cur == 'KRW'?'selected':''}} value="LKR">South Korean won (KRW)</option>
                                <option {{$item->cur == 'UZS'?'selected':''}} value="UZS">Uzbekistan (UZS)</option>
                                <option {{$item->cur == 'KZT'?'selected':''}} value="KZT">Kazakhstan (KZT)</option>
                                <option {{$item->cur == 'KES'?'selected':''}} value="KES">Kenyan Shillings (KES)</option>
                                <option {{$item->cur == 'THB'?'selected':''}} value="THB">Thai baht (THB)</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Exchange Rate Category</label>
                            <select onchange="exchange_rate()" class="select2 form-control" name="exc_{{$itemCount}}" required id="exchange_rate_option">
                                <option value="0">Fetch From API</option>
                                <option value="1">Enter Manually</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2 mb-2" id="manual_exchange" style="display: none">
                            <label>Exchange Rate</label>
                            <input type="text" class="form-control" value="0" name="manual_exchange_rate_{{$itemCount}}"
                                   required>
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Amount paid</label>
                            <input type="text" class="form-control" value="{{$item->forex_amount}}"
                                   name="forex_amount_{{$itemCount}}" id="forex_amount_{{$itemCount}}" required>
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Forex Amount</label>
                            <input type="text" class="form-control" value="{{$item->amount}}"
                                   name="amount_{{$itemCount}}" id="amount_{{$itemCount}}" readonly>
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Receipt provided</label>
                            <select class="select2 form-control" name="receipt_provided_{{$itemCount}}" required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2 mb-2" hidden>
                            <label>Category</label>
                            <input type="text" class="form-control" value="{{$categoryID}}"
                                   name="category_{{$itemCount}}" id="category_{{$itemCount}}" readonly>
                        </div>
                        <div class="form-group col-md-1" style="margin-top: 2.0rem">
                            <a class="btn btn-danger btn-sm white" id="removeCategoryItem"><i class="la la-trash-o"></i></a>
                        </div>
                    </div>
                @endforeach
                {{--*/ View::share('itemCount', $itemCount) /*--}}
            @else
                <div class="form-row">
                    <div class="form-group col-md-2 mb-2">
                        <label>Date</label>
                        <input type="date" class="form-control" name="date_{{$itemCount}}"
                               min="{{date('Y-m-d', strtotime(date('Y-m-d').' - 3 months'))}}" id="date_{{$itemCount}}"
                               required>
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <label>Description of expense</label>
                        <select class="select2 form-control" name="description_{{$itemCount}}" required>
                            <option value="Taxi">Taxi</option>
                            <option value="Train">Train</option>
                            <option value="Air">Air</option>
                            <option value="Sim charges">Sim charges</option>
                            <option value="Other charges">Other charges</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <label>Currency</label>
                        <select class="select2 form-control" name="cur_{{$itemCount}}" required>
                            <option value="EUR">EUR</option>
                            <option value="USD">USD</option>
                            <option value="CAD">CAD</option>
                            <option value="ZAR">ZAR</option>
                            <option value="GBP">GBP</option>
                            <option value="AED">Dirham</option>
                            <option value="CHF">Swiss Franc</option>
                            <option value="SEK">Swedish Krona</option>
                            <option value="SAR">Riyal</option>
                            <option value="TRY">Turkish Lira</option>
                            <option value="CNY">Chinese Yuan</option>
                            <option value="LKR">Sri Lankan Rupee (LKR)</option>
                            <option value="KRW">South Korean won (KRW)</option>
                            <option value="UZS">Uzbekistan (UZS)</option>
                            <option value="KZT">Kazakhstan (KZT)</option>
                            <option value="KES">Kenyan Shillings (KES)</option>
                            <option value="THB">Thai baht (THB)</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <label>Exchange Rate Category</label>
                        <select onchange="exchange_rate()" class="select2 form-control" name="exc_{{$itemCount}}" id="exchange_rate_option" required>
                            <option value="0">From API</option>
                            <option value="1">Enter Manually</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2 mb-2" id="manual_exchange" style="display: none">
                        <label>Exchange Rate</label>
                        <input type="text" class="form-control" value="0" name="manual_exchange_rate_{{$itemCount}}"
                               id="manual_exchange_rate_{{$itemCount}}" required>
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <label>Amount paid</label>
                        <input type="text" class="form-control" value="0" name="forex_amount_{{$itemCount}}"
                               id="forex_amount_{{$itemCount}}" required>
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <label>Forex Amount</label>
                        <input type="text" class="form-control" value="0" name="amount_{{$itemCount}}"
                               id="amount_{{$itemCount}}" readonly>
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <label>Receipt provided</label>
                        <select class="select2 form-control" name="receipt_provided_{{$itemCount}}" required>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2 mb-2" hidden>
                        <label>Category</label>
                        <input type="text" class="form-control" value="{{$categoryID}}" name="category_{{$itemCount}}"
                               id="category_{{$itemCount}}" readonly>
                    </div>
                    <div class="form-group col-md-1" style="margin-top: 2.0rem">
                        <a class="btn btn-danger btn-sm white" id="removeCategoryItem"><i class="la la-trash-o"></i></a>
                    </div>
                </div>
            @endif
        </div>
        <div class="card-footer">
            <a class="btn btn-info btn-sm white" id="addVoucherItem" data-category="{{$categoryName}}"
               data-category-id="{{$categoryID}}"><i class="la la-plus-circle"></i></a>
        </div>
    </div>
</div>
<script>
    function exchange_rate() {
        if (document.getElementById('exchange_rate_option').value == 1) {
            document.getElementById('manual_exchange').style.display = "block"
        } else {
            document.getElementById('manual_exchange').style.display = "none"
        }
    }
</script>
