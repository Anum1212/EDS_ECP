<div class="form-row">
    <div class="form-group col-md-2 mb-2">
        <label>Date of Receipt</label>
        <input type="date" class="form-control" name="date_{{$itemCount}}" min="{{date('Y-m-d', strtotime(date('Y-m-d').' - 3 months'))}}" id="date_{{$itemCount}}" required>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Receipt #</label>
        <input type="text" class="form-control" name="receipt_number_{{$itemCount}}" id="receipt_number_{{$itemCount}}" required>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Vehicle #</label>
        <select class="select2 form-control" name="vehicle_no_{{$itemCount}}" id="vehicle_no_{{$itemCount}}" required>
            @foreach($employee->vehicles as $vehicle)
                <option value="{{$vehicle->vehicle_number}}">{{$vehicle->vehicle_number}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Official Travel</label>
        <select class="select2 form-control official_travel" name="official_travel_{{$itemCount}}" id="official_travel_{{$itemCount}}" required>
            <option value="No">No</option>
            <option value="Yes">Yes</option>
        </select>
    </div>
    <div class="form-group col-md-2 mb-2" hidden>
        <label>Amount (PKR)</label>
        <input type="text" class="form-control" value="0" name="amount_{{$itemCount}}" id="amount_{{$itemCount}}" readonly>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Amount paid</label>
        <input type="number" class="form-control" min="1" data-id='225' name="amount_paid_{{$itemCount}}" id="amount_paid_{{$itemCount}}" placeholder="PKR" required>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Rate/Litre</label>
        <input type="number" class="form-control" min="1" value="0" name="rate_per_litre_{{$itemCount}}" id="rate_per_litre_{{$itemCount}}" step="0.01" required>
    </div>
    <div class="form-group col-md-1 mb-2">
        <label>Litres</label>
        <input type="text" class="form-control" value="0" name="litres_{{$itemCount}}" id="litres_{{$itemCount}}" readonly required>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Receipt provided</label>
        <select class="select2 form-control" name="receipt_provided_{{$itemCount}}" required>
            <option value="1">Yes</option>
            <option value="0">No, lost</option>
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