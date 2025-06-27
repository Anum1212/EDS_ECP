<div class="form-row">
    <div class="form-group col-md-2 mb-2">
        <label>Date</label>
        <input type="date" class="form-control meal-out-office-date" name="date_{{$itemCount}}" min="{{date('Y-m-d', strtotime(date('Y-m-d').' - 3 months'))}}" id="date_{{$itemCount}}" required>
    </div>
    <div class="form-group col-md-5 mb-2">
        <label>Meal Dates</label>
        <input type="text" class="form-control mealOutOfLahore" name="meal_dates_{{$itemCount}}" id="meal_dates_{{$itemCount}}" readonly required>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Description</label>
        <select class="select2 form-control" name="description_{{$itemCount}}" required>
            <option value="">Please Select</option>
            <option value="Lunch">Lunch</option>
            <option value="Dinner">Dinner</option>
        </select>
    </div>
    <div class="form-group col-md-2 mb-2">
        <label>Amount (PKR)</label>
        <input type="text" class="form-control" value="0" name="amount_{{$itemCount}}" id="amount_{{$itemCount}}" readonly>
    </div>
    <div class="form-group col-md-2 mb-2" hidden>
        <label>Category</label>
        <input type="text" class="form-control" value="{{$categoryID}}" name="category_{{$itemCount}}" id="category_{{$itemCount}}" readonly>
    </div>
    <div class="form-group col-md-1" style="margin-top: 2.0rem">
        <a class="btn btn-danger btn-sm white" id="removeCategoryItem"><i class="la la-trash-o"></i></a>
    </div>
</div>