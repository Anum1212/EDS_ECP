<div class="card" id="{{$categoryName}}" data-parent="{{$parent}}">
    <div class="card-header card-head-inverse bg-secondary">
        <a data-action="collapse"><h4 class="card-title text-white">Local - DA (via Air) <span class="badge badge bg-white text-secondary badge-pill mr-2"></span></h4></a>
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
            <p class="danger"><strong>Don't forget to attach approved Travel Order form with voucher print in case of air travel</strong></p>
            <hr>
            @if(isset($voucher))
                @foreach($voucher->categoryItems($voucher->id,$categoryID) as $item)
                    {{--*/ $itemCount++ /*--}}
                    <div class="form-row">
                        <div class="form-group col-md-4 mb-2">
                            <label>Duration of Trip</label>
                            <div class='input-group'>
                                <input type='text' class="form-control datetime" name="date_range_{{$itemCount}}" value="{{date('m/d/Y H:i', strtotime($item->date_from)).' - '.date('m/d/Y H:i', strtotime($item->date_to))}}" required>
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
                            <label>Accommodation Required</label>
                            <select class="select2 form-control" name="hotel_required_{{$itemCount}}" required>
                                <option {{$item->hotel_required == '0'?'selected':''}} value="0">No</option>
                                <option {{$item->hotel_required == '0'?'selected':''}} value="1">Yes</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Eligible period for DA</label>
                            <input type="text" class="form-control" data-id='225' value="{!! \App\Http\Controllers\VoucherController::calculateDaysVoucherCopy($item->date_from, $item->date_to) !!}" name="da_eligible_period_{{$itemCount}}" id="da_eligible_period_{{$itemCount}}" readonly>
                        </div>
                        <div class="form-group col-md-2 mb-2" hidden>
                            <label>Category</label>
                            <input type="text" class="form-control" value="{{$categoryID}}" name="category_{{$itemCount}}" id="category_{{$itemCount}}" readonly>
                        </div>
                        <div class="form-group col-md-2" style="margin-top: 2.0rem">
                            <a class="btn btn-danger btn-sm white" id="removeCategoryItem"><i class="la la-trash-o"></i></a>
                        </div>
                    </div>
                @endforeach
                {{--*/ View::share('itemCount', $itemCount) /*--}}
            @else
                <div class="form-row">
                    <div class="form-group col-md-4 mb-2">
                        <label>Duration of Trip</label>
                        <div class='input-group'>
                            <input type='text' class="form-control datetime" name="date_range_{{$itemCount}}" required>
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
                        <label>Accommodation Required</label>
                        <select class="select2 form-control" name="hotel_required_{{$itemCount}}" required>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <label>Eligible period for DA</label>
                        <input type="text" class="form-control" data-id='225' name="da_eligible_period_{{$itemCount}}" id="da_eligible_period_{{$itemCount}}" readonly>
                    </div>
                    <div class="form-group col-md-2 mb-2" hidden>
                        <label>Category</label>
                        <input type="text" class="form-control" value="{{$categoryID}}" name="category_{{$itemCount}}" id="category_{{$itemCount}}" readonly>
                    </div>
                    <div class="form-group col-md-2" style="margin-top: 2.0rem">
                        <a class="btn btn-danger btn-sm white" id="removeCategoryItem"><i class="la la-trash-o"></i></a>
                    </div>
                </div>
            @endif
        </div>
        <div class="card-footer">
            <a class="btn btn-info btn-sm white" id="addVoucherItem" data-category="{{$categoryName}}" data-category-id="{{$categoryID}}"><i class="la la-plus-circle"></i></a>
        </div>
    </div>
</div>
