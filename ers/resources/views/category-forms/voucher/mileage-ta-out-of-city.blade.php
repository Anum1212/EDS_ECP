<div class="card" id="{{$categoryName}}" data-parent="{{$parent}}">
    <!--<div class="card-header card-head-inverse bg-secondary">-->
        <div class="card-header card-head-inverse {{count($employee->vehicles) > 0?'bg-danger':'bg-secondary'}}">
        <a data-action="collapse"><h4 class="card-title text-white">Fuel - Mileage (out of city)<span class="badge badge bg-white text-secondary badge-pill mr-2"></span></h4></a>
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
            @if(count($employee->vehicles) < 1)
            @if(isset($voucher))
                @foreach($voucher->categoryItems($voucher->id,$categoryID) as $item)
                    {{--*/ $itemCount++ /*--}}
                    <div class="form-row">
                        <div class="form-group col-md-2 mb-2">
                            <label>Date of Travel</label>
                            <input type="date" class="form-control" name="date_{{$itemCount}}" id="date_{{$itemCount}}" min="{{date('Y-m-d', strtotime(date('Y-m-d').' - 3 months'))}}" value="{{date('Y-m-d', strtotime($item->date_from))}}" required>
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>From Location</label>
                            <input type="text" class="form-control" data-id='225' name="from_{{$itemCount}}" id="from_{{$itemCount}}" value="{{$item->from}}" required>
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>To Location</label>
                            <input type="text" class="form-control" data-id='225' name="to_{{$itemCount}}" id="to_{{$itemCount}}" value="{{$item->to}}" required>
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Mode of Travel</label>
                            <select class="select2 form-control" name="mode_of_travel_{{$itemCount}}" required>
                                <option {{$item->travel_mode == "Car"?"Selected":""}} value="Car">Car</option>
                                <option {{$item->travel_mode == "Bike"?"Selected":""}} value="Bike">Bike</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Distance Travelled (Km)</label>
                            <input type="text" class="form-control" data-id='225' name="kms_{{$itemCount}}" id="kms_{{$itemCount}}" value="{{$item->kms}}" required>
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Amount (PKR)</label>
                            <input type="text" class="form-control" value="{{$item->amount}}" name="amount_{{$itemCount}}" id="amount_{{$itemCount}}" readonly>
                        </div>
                        <div class="form-group col-md-2 mb-2" hidden>
                            <label>Category</label>
                            <input type="text" class="form-control" value="{{$categoryID}}" name="category_{{$itemCount}}" id="category_{{$itemCount}}">
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
                        <input type="text" class="form-control" data-id='225' name="kms_{{$itemCount}}" id="kms_{{$itemCount}}" required>
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
             @else
                <p>Fuel Card holders cannot claim mileage as per Company Policy!</p>
                @endif
        </div>
        <div class="card-footer">
            <a class="btn btn-info btn-sm white" id="addVoucherItem" data-category="{{$categoryName}}" data-category-id="{{$categoryID}}"><i class="la la-plus-circle"></i></a>
        </div>
    </div>
</div>
