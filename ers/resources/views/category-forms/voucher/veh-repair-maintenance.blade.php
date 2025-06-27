<div class="card" id="{{$categoryName}}" data-parent="{{$parent}}">
    <div class="card-header card-head-inverse {{count($employee->vehicles) == 0?'bg-danger':'bg-secondary'}}">
        <a data-action="collapse"><h4 class="card-title text-white">Vehicle Repair/Maintenance <span class="badge badge bg-white text-secondary badge-pill mr-2"></span></h4></a>
        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                <li><a data-action="collapse"><i class="ft-plus"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="card-content collapse" style="overflow-x: auto">
        <div class="card-body">
            @if(count($employee->vehicles) > 0)
                @if(isset($voucher))
                    @foreach($voucher->categoryItems($voucher->id,$categoryID) as $item)
                        {{--*/ $itemCount++ /*--}}
                        <div class="form-row">
                            <div class="form-group col-md-2 mb-2">
                                <label>Date</label>
                                <input type="date" class="form-control" name="date_{{$itemCount}}" id="date_{{$itemCount}}" min="{{date('Y-m-d', strtotime(date('Y-m-d').' - 3 months'))}}" value="{{date('Y-m-d', strtotime($item->date_from))}}" required>
                            </div>
                            <div class="form-group col-md-2 mb-2">
                                <label>Vehicle #</label>
                                <select class="select2 form-control" name="vehicle_no_{{$itemCount}}" id="vehicle_no_{{$itemCount}}" required>
                                    @foreach($employee->vehicles as $vehicle)
                                        <option {{$item->vehicle_number == $vehicle->vehicle_number ?'selected':''}} value="{{$vehicle->vehicle_number}}">{{$vehicle->vehicle_number}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4 mb-2">
                                <label>Description</label>
                                <input type="tex" class="form-control" data-id='225' name="description_{{$itemCount}}" id="description_{{$itemCount}}" value="{{$item->description}}" required>
                            </div>

                            <div class="form-group col-md-2 mb-2" hidden>
                                <label>Amount (PKR)</label>
                                <input type="text" class="form-control" value="{{$item->amount}}" name="amount_{{$itemCount}}" id="amount_{{$itemCount}}" {{$item->amount}}>
                            </div>
                            <div class="form-group col-md-2 mb-2">
                                <label>Amount paid</label>
                                <input type="text" class="form-control" value="{{$item->amount_paid}}" name="amount_paid_{{$itemCount}}" id="amount_paid_{{$itemCount}}" {{$item->amount_paid}} required>
                            </div>
                            <div class="form-group col-md-2 mb-2">
                                <label>Receipt provided</label>
                                <select class="select2 form-control" name="receipt_provided_{{$itemCount}}" required>
                                    <option {{$item->receipt_copy == 1?'selected':''}} value="1">Yes</option>
                                    <option {{$item->receipt_copy == 0?'selected':''}} value="0">No</option>
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
                    @endforeach
                    {{--*/ View::share('itemCount', $itemCount) /*--}}
                @else
                    <div class="form-row">
                        <div class="form-group col-md-2 mb-2">
                            <label>Date</label>
                            <input type="date" class="form-control" name="date_{{$itemCount}}" min="{{date('Y-m-d', strtotime(date('Y-m-d').' - 3 months'))}}" id="date_{{$itemCount}}" required>
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Vehicle #</label>
                            <select class="select2 form-control" name="vehicle_no_{{$itemCount}}" id="vehicle_no_{{$itemCount}}" required>
                                @foreach($employee->vehicles as $vehicle)
                                    <option value="{{$vehicle->vehicle_number}}">{{$vehicle->vehicle_number}}</option>
                                @endforeach
                            </select>
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
                @endif
            @else
                <p>No vehicle found against your Employment Details.</p>
            @endif
        </div>
        <div class="card-footer">
            <a class="btn btn-info btn-sm white" id="addVoucherItem" data-category="{{$categoryName}}" data-category-id="{{$categoryID}}"><i class="la la-plus-circle"></i></a>
        </div>
    </div>
</div>
