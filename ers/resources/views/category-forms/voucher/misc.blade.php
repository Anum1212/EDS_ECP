{{--*/ $categories = \App\Category::where('company_id',$employee->department->businessUnit->company->id)->get(); /*--}}

<div id="{{$categoryName}}" data-parent="{{$parent}}">
    <!-- Label bar (only appears once, not cloned) -->
    <div class="label-bar col-md-12">
        <div class="col-md-2 label">Category<span class="required">*</span></div>
        <div class="col-md-2 label">Date<span class="required">*</span></div>
        <div class="col-md-3 label">Description<span class="required">*</span></div>
        <div class="col-md-2 label">Amount Paid<span class="required">*</span></div>
        <div class="col-md-2 label">Receipt Provided<span class="required">*</span></div>
    </div>

    <div id="form-rows">
        @if(isset($voucher))
            @foreach($voucher->categoryItems($voucher->id, $categoryID) as $item)
                {{--*/ $itemCount++ /*--}}
                <div class="form-row">
                    <!-- Input fields under the label bar -->
                    <div class="form-group col-md-2">
                        <select name="category_id[]" class="form-control custom-input" style="
                        height: 50%;
                    ">
                            <option value="" disabled selected>Select Category</option>
                            @foreach($categories as $category)
                                @if($category->enabled)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <input type="date" class="form-control custom-input" name="date_{{$itemCount}}" id="date_{{$itemCount}}" min="{{date('Y-m-d', strtotime(date('Y-m-d').' - 3 months'))}}" value="{{date('Y-m-d', strtotime($item->date_from))}}" required>
                    </div>
                    <div class="form-group col-md-3 mb-2">
                        <input type="text" class="form-control custom-input" name="description_{{$itemCount}}" id="description_{{$itemCount}}" value="{{$item->description}}" required>
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <input type="text" class="form-control custom-input" value="{{$item->amount_paid}}" name="amount_paid_{{$itemCount}}" id="amount_paid_{{$itemCount}}" required>
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <select class="form-control select-custom" name="receipt_provided_{{$itemCount}}" required style="
                        height: 50%;
                    ">
                            <option {{$item->receipt_copy == 1 ? 'selected' : ''}} value="1">Yes</option>
                            <option {{$item->receipt_copy == 0 ? 'selected' : ''}} value="0">No</option>
                        </select>
                    </div>
                    <div class="form-group col-md-1" style="margin-top: 2rem">
                        <a class="btn custom-button remove-row"><i class="la la-trash-o light-blue-icon"></i></a>
                    </div>
                </div>
            @endforeach
            {{--*/ View::share('itemCount', $itemCount) /*--}}
        @else
            <!-- Template Row for Cloning -->
            <div class="form-row" id="template-row">
                <div class="form-group col-md-2">
                    <select name="category_id[]" class="form-control custom-input" style="
                    height: 50%;
                ">
                        <option value="" disabled selected>Select Category</option>
                        @foreach($categories as $category)
                            @if($category->enabled)
                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2 mb-2">
                    <input type="date" class="form-control custom-input" name="date[]" required>
                </div>
                <div class="form-group col-md-3 mb-2">
                    <input type="text" class="form-control custom-input" name="description[]" required>
                </div>
                <div class="form-group col-md-2 mb-2">
                    <input type="text" class="form-control custom-input" value="0" name="amount_paid[]" required>
                </div>
                <div class="form-group col-md-2 mb-2">
                    <select class="form-control select-custom" name="receipt_provided[]" required style="
                    height: 50%;
                ">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="form-group col-md-1">
                    <a class="btn custom-button remove-row"><i class="la la-trash-o light-blue-icon"></i></a>
                </div>
            </div>
        @endif
    </div>

    <!-- Add Button -->
    <div class="form-row mt-3">
        <div class="col-md-12 text-left">
            <button type="button" class="btn btn-light btn-sm rounded" id="add-row-btn">Add</button>
        </div>
    </div>
</div>

<style>
.label-bar {
    display: flex;
    align-items: center;
    background-color: #f0f0f0;
    padding: 8px;
    margin-bottom: 8px;
    border-radius: 4px;
}

.label-bar .label {
    padding: 0 8px;
    font-weight: bold;
    text-align: left;
}

.required {
    color: red;
    font-weight: bold;
    margin-right: 4px;
}

.custom-input, .select-custom {
    height: 35px;
    box-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
    transition: box-shadow 0.2s ease;
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.custom-input:hover, .select-custom:hover {
    box-shadow: 0 0 4px rgba(0, 0, 0, 0.7);
}

.custom-button {
    background-color: transparent;
    border: none;
    color: inherit;
    padding: 5px 10px;
}

.light-blue-icon {
    color: #03A2DD;
}

.btn-light {
    background-color: #e0e0e0;
    border: none;
}

.btn-light.btn-sm {
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
}

.btn-light:hover {
    background-color: #d4d4d4;
}

.form-control, .custom-input {
    height: 35%;
}
</style>

<script>
document.getElementById('add-row-btn').addEventListener('click', function() {
    // Clone only the input fields row and clear the values
    var templateRow = document.getElementById('template-row').cloneNode(true);
    templateRow.removeAttribute('id'); // Remove the id from the cloned row
    templateRow.querySelectorAll('input').forEach(input => input.value = ''); // Clear inputs
    templateRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0); // Reset selects
    
    // Append the cloned row to the form-rows container
    document.getElementById('form-rows').appendChild(templateRow);
});

// Remove a row when the trash icon is clicked
document.getElementById('form-rows').addEventListener('click', function(event) {
    if (event.target.closest('.remove-row')) {
        event.target.closest('.form-row').remove();
    }
});
</script>
