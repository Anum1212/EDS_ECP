<div class="form-row">
    <div class="form-group col-md-4 mb-2">
        <label>Duration of Trip</label>
        <div class='input-group'>
            <input type='text' class="form-control datetime" name="date_range_{{$itemCount}}" required/>
            <div class="input-group-append">
                <span class="input-group-text">
                  <span class="la la-calendar"></span>
                </span>
            </div>
        </div>
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
        <label>Total Distance (Km)</label>
        <input type="text" class="form-control" value="0" name="kms_{{$itemCount}}" id="kms_{{$itemCount}}" required>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Eligible period for DA</label>
        <input type="text" class="form-control" data-id='225' name="da_eligible_period_{{$itemCount}}" id="da_eligible_period_{{$itemCount}}" readonly>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Amount (PKR)</label>
        <input type="text" class="form-control" value="0" name="amount_{{$itemCount}}" id="amount_{{$itemCount}}" readonly>
    </div>
    <div class="form-group col-md-2 mb-2" hidden>
        <label>Category</label>
        <input type="text" class="form-control" value="{{$categoryID}}" name="category_{{$itemCount}}" id="category_{{$itemCount}}" readonly>
    </div>
    <div class="form-group col-md-2" style="margin-top: 2.0rem">
        <a class="btn btn-danger btn-sm white" id="removeCategoryItem"><i class="la la-trash-o"></i></a>
    </div>
</div>