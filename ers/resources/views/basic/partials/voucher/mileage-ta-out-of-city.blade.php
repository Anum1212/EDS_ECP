@if(count($employee->vehicles) < 1)
<div class="form-row">
    <div class="form-group col-md-2 mb-2">
        <label>Date of Travel</label>
        <input type="date" class="form-control" name="date_{{$itemCount}}" min="{{date('Y-m-d', strtotime(date('Y-m-d').' - 3 months'))}}" id="date_{{$itemCount}}" required>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>From Location</label>
        <input type="text" class="form-control" data-id='225' name="from_{{$itemCount}}" id="from_{{$itemCount}}" required>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>To Location</label>
        <input type="text" class="form-control" data-id='225' name="to_{{$itemCount}}" id="to_{{$itemCount}}" required>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Mode of Travel</label>
        <select class="select2 form-control" name="mode_of_travel_{{$itemCount}}" required>
            <option value="Car">Car</option>
            <option value="Bike">Bike</option>
        </select>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Distance Travelled (Km)</label>
        <input type="text" class="form-control" name="kms_{{$itemCount}}" id="kms_{{$itemCount}}" required>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Amount (PKR)</label>
        <input type="text" class="form-control" value="0" name="amount_{{$itemCount}}" id="amount_{{$itemCount}}" readonly>
    </div>
    <div class="form-group col-md-2 mb-2" hidden>
        <label>Category</label>
        <input type="text" class="form-control" value="{{$categoryID}}" name="category_{{$itemCount}}" id="category_{{$itemCount}}">
    </div>
    <div class="form-group col-md-1" style="margin-top: 2.0rem">
        <a class="btn btn-danger btn-sm white" id="removeCategoryItem"><i class="la la-trash-o"></i></a>
    </div>
</div>
@endif