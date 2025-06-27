<div class="form-row">
    <div class="form-group col-md-4 mb-2">
        <select class="select2 form-control voucherCategory" name="voucher_categories_{{$count}}" id="voucher_categories_{{$count}}" required>
            <option value="">Select Category</option>
            @foreach($categories as $category)
                <option value="{{$category->id}}" data-view="{{$category->view}}">{{$category->category_name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-2 mb-2">
        <input type="text" class="form-control" name="total_amount_{{$count}}" id="total_voucher_categories_{{$count}}" placeholder="Total Amount (PKR)" readonly>
    </div>
    <div class="col-md-2">
        <a class="btn btn-danger btn-sm white" id="removeCategory"><i class="la la-trash-o"></i></a>
    </div>
</div>