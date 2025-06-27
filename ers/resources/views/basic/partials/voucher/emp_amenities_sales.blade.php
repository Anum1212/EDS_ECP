<div class="form-row">
    <div class="form-group col-md-2 mb-2">
        <label>Date</label>
        <input type="date" class="form-control" name="date_{{$itemCount}}" min="{{date('Y-m-d', strtotime(date('Y-m-d').' - 3 months'))}}" id="date_{{$itemCount}}" required>
    </div>
    <div class="form-group col-md-4 mb-2">
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