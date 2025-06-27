<div class="form-row">
    <div class="form-group col-md-4 mb-2">
        <label>Duration of Trip</label>
        <div class='input-group'>
            <input type='text' class="form-control datetime" name="date_range_{{$itemCount}}"  required/>
            <div class="input-group-append">
                <span class="input-group-text">
                  <span class="la la-calendar"></span>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Personal Days</label>
        <input type="text" class="form-control" value="0" name="personal_days_{{$itemCount}}" id="personal_days_{{$itemCount}}" required>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Other Information</label>
        <select class="select2 form-control" name="accomodation_food_{{$itemCount}}" required>
            <option></option>
            <option value="Own Accomodation">Own Accomodation</option>
            <option value="Accomodation by Host">Accomodation only by Host</option>
            <option value="Both Accomodation & Food by Host">Both Accomodation & Food by Host</option>
        </select>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Purpose</label>
        <select class="select2 form-control" name="purpose_{{$itemCount}}" required>
            <option value="Meeting">Meeting</option>
            <option value="Training">Training</option>
            <option value="Exhibition">Exhibition</option>
        </select>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Eligible period for DA</label>
        <input type="number" class="form-control" data-id='225' name="da_eligible_period_{{$itemCount}}" id="da_eligible_period_{{$itemCount}}" readonly>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Amount (USD)</label>
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