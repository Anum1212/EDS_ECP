<div class="form-row">
    <div class="form-group col-md-2 mb-2">
        <label>Date</label>
        <input type="date" class="form-control" name="date_{{$itemCount}}" min="{{date('Y-m-d', strtotime(date('Y-m-d').' - 3 months'))}}" id="date_{{$itemCount}}" required>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Month</label>
        <select class="select2 form-control" name="month_{{$itemCount}}" required>
            <option value="January">January</option>
            <option value="February">February</option>
            <option value="March">March</option>
            <option value="April">April</option>
            <option value="May">May</option>
            <option value="June">June</option>
            <option value="July">July</option>
            <option value="August">August</option>
            <option value="September">September</option>
            <option value="October">October</option>
            <option value="November">November</option>
            <option value="December">December</option>
        </select>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Description</label>
        <input type="tex" class="form-control" data-id='225' name="description_{{$itemCount}}" id="description_{{$itemCount}}" required>
    </div>
    <div class="form-group col-md-2 mb-2" hidden>
        <label>Amount (PKR)</label>
        <input type="text" class="form-control" value="0" name="amount_{{$itemCount}}" id="amount_{{$itemCount}}">
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Amount paid</label>
        <input type="text" class="form-control" value="0" name="amount_paid_{{$itemCount}}" id="amount_paid_{{$itemCount}}" required>
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