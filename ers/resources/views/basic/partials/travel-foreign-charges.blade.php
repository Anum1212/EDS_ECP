<div class="form-row">
    <div class="form-group col-md-2 mb-2">
        <label>Date</label>
        <input type="date" class="form-control" name="date_{{$itemCount}}" min="{{date('Y-m-d', strtotime(date('Y-m-d').' - 3 months'))}}" id="date_{{$itemCount}}" required>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Description of cxpense</label>
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
            <option value="AED">Dirham</option>
            <option value="CHF">Swiss Franc</option>
            <option value="SEK">Swedish krona</option>
            <option value="SAR">Riyal</option>
            <option value="TRY">Turkish Lira</option>
            <option value="CNY">Chinese Yuan</option>
            <option value="LKR">Srilankan Rupee</option>
            <option value="KRW">South Korean won</option>
            <option value="SGD">Singapore Dollar</option>
            <option value="UZS">Uzbekistan (UZS)</option>
            <option value="KZT">Kazakhstan (KZT)</option>
            <option value="KES">Kenyan Shillings (KES)</option>
            <option value="THB">Thai baht (THB)</option>

        </select>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Exchange Rate Category</label>
        <select onchange="exchange_rate()" class="select2 form-control" name="exc_{{$itemCount}}" required id="exc_{{$itemCount}}">
            <option value="0">Fetch From API</option>
            <option value="1">Enter Manually</option>
        </select>
    </div>
    <div class="form-group col-md-2 mb-2" id="manual_exchange_{{$itemCount}}" style="display: none">
        <label>Exchange Rate</label>
        <input type="text" class="form-control" value="0" name="manual_exchange_rate_{{$itemCount}}"
               required>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Amount paid</label>
        <input type="text" class="form-control" value="0" name="forex_amount_{{$itemCount}}" id="forex_amount_{{$itemCount}}" required>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Forex Amount</label>
        <input type="text" class="form-control" value="0" name="amount_{{$itemCount}}" id="amount_{{$itemCount}}" readonly>
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
        <input type="text" class="form-control" value="{{$categoryID}}" name="category_{{$itemCount}}" id="category_{{$itemCount}}" readonly>
    </div>
    <div class="form-group col-md-1" style="margin-top: 2.0rem">
        <a class="btn btn-danger btn-sm white" id="removeCategoryItem"><i class="la la-trash-o"></i></a>
    </div>
</div>
<script>
    function exchange_rate() {
        if (document.getElementById('exc_{{$itemCount}}').value == 1) {
            document.getElementById('manual_exchange_{{$itemCount}}').style.display = "block"
        } else {
            document.getElementById('manual_exchange_{{$itemCount}}').style.display = "none"
        }
    }
</script>