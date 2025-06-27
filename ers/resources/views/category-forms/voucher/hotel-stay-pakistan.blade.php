<!--<div class="card" id="{{$categoryName}}" data-parent="{{$parent}}">-->
<!--    <div class="card-header card-head-inverse bg-secondary">-->
<!--        <a data-action="collapse"><h4 class="card-title text-white">Local - Hotel Stay<span class="badge badge bg-white text-secondary badge-pill mr-2"></span></h4></a>-->
<!--        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>-->
<!--        <div class="heading-elements">-->
<!--            <ul class="list-inline mb-0">-->
<!--                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>-->
<!--                <li><a data-action="collapse"><i class="ft-plus"></i></a></li>-->
<!--            </ul>-->
<!--        </div>-->
<!--    </div>-->
<!--    <div class="card-content collapse" style="overflow-x: auto">-->
<!--        <div class="card-body">-->
<!--            <p class="danger"><strong>Please write complete detail of expense in description</strong></p>-->
<!--            <hr>-->
<!--            @if(isset($voucher))-->
<!--                @foreach($voucher->categoryItems($voucher->id,$categoryID) as $item)-->
<!--                    {{--*/ $itemCount++ /*--}}-->
<!--                    <div class="form-row">-->
                        <!--<div class="form-group col-md-2 mb-2">-->
                        <!--    <label>Date</label>-->
                        <!--    <input type="date" class="form-control" name="date_{{$itemCount}}" id="date_{{$itemCount}}" min="{{date('Y-m-d', strtotime(date('Y-m-d').' - 3 months'))}}" value="{{date('Y-m-d', strtotime($item->date_from))}}" required>-->
                        <!--</div>-->
<!--                        <div class="form-group col-md-4 mb-2">-->
<!--                            <label>Duration of Stay</label>-->
<!--                            <div class='input-group'>-->
<!--                                <input type='text' class="form-control datetime" name="date_range_{{$itemCount}}" value="{{date('m/d/Y H:i', strtotime($item->date_from)).' - '.date('m/d/Y H:i', strtotime($item->date_to))}}" required>-->
<!--                                <div class="input-group-append">-->
<!--                            <span class="input-group-text">-->
<!--                              <span class="la la-calendar"></span>-->
<!--                            </span>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="form-group col-md-2 mb-2" hidden>-->
<!--                            <label>Amount (PKR)</label>-->
<!--                            <input type="text" class="form-control" value="{{$item->amount}}" name="amount_{{$itemCount}}" id="amount_{{$itemCount}}" {{$item->amount}}>-->
<!--                        </div>-->
<!--                        <div class="form-group col-md-2 mb-2">-->
<!--                            <label>Amount paid</label>-->
<!--                            <input type="text" class="form-control" value="{{$item->amount_paid}}" name="amount_paid_{{$itemCount}}" id="amount_paid_{{$itemCount}}" {{$item->amount_paid}} required>-->
<!--                        </div>-->
<!--                        <div class="form-group col-md-2 mb-2">-->
<!--                            <label>Receipt provided</label>-->
<!--                            <select class="select2 form-control" name="receipt_provided_{{$itemCount}}" required>-->
<!--                                <option {{$item->receipt_copy == 1?'selected':''}} value="1">Yes</option>-->
<!--                                <option {{$item->receipt_copy == 0?'selected':''}} value="0">No</option>-->
<!--                            </select>-->
<!--                        </div>-->
<!--                        <div class="form-group col-md-2 mb-2" hidden>-->
<!--                            <label>Category</label>-->
<!--                            <input type="text" class="form-control" value="{{$categoryID}}" name="category_{{$itemCount}}" id="category_{{$itemCount}}" readonly>-->
<!--                        </div>-->
<!--                        <div class="form-group col-md-1" style="margin-top: 2.0rem">-->
<!--                            <a class="btn btn-danger btn-sm white" id="removeCategoryItem"><i class="la la-trash-o"></i></a>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                @endforeach-->
<!--                {{--*/ View::share('itemCount', $itemCount) /*--}}-->
<!--            @else-->
<!--                <div class="form-row">-->
                    <!--<div class="form-group col-md-2 mb-2">-->
                    <!--    <label>Date</label>-->
                    <!--    <input type="date" class="form-control" name="date_{{$itemCount}}" min="{{date('Y-m-d', strtotime(date('Y-m-d').' - 3 months'))}}" id="date_{{$itemCount}}" required>-->
                    <!--</div>-->
<!--                    <div class="form-group col-md-4 mb-2">-->
<!--                        <label>Duration of Trip</label>-->
<!--                        <div class='input-group'>-->
<!--                            <input type='text' class="form-control datetime" name="date_range_{{$itemCount}}" required>-->
<!--                            <div class="input-group-append">-->
<!--                            <span class="input-group-text">-->
<!--                              <span class="la la-calendar"></span>-->
<!--                            </span>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="form-group col-md-2 mb-2" hidden>-->
<!--                        <label>Amount (PKR)</label>-->
<!--                        <input type="text" class="form-control" value="0" name="amount_{{$itemCount}}" id="amount_{{$itemCount}}">-->
<!--                    </div>-->
<!--                    <div class="form-group col-md-2 mb-2">-->
<!--                        <label>Amount paid</label>-->
<!--                        <input type="text" class="form-control" value="0" name="amount_paid_{{$itemCount}}" id="amount_paid_{{$itemCount}}" required>-->
<!--                    </div>-->
<!--                    <div class="form-group col-md-2 mb-2">-->
<!--                        <label>Receipt provided</label>-->
<!--                        <select class="select2 form-control" name="receipt_provided_{{$itemCount}}" required>-->
<!--                            <option value="1">Yes</option>-->
<!--                            <option value="0">No</option>-->
<!--                        </select>-->
<!--                    </div>-->
<!--                    <div class="form-group col-md-2 mb-2" hidden>-->
<!--                        <label>Category</label>-->
<!--                        <input type="text" class="form-control" value="{{$categoryID}}" name="category_{{$itemCount}}" id="category_{{$itemCount}}" readonly>-->
<!--                    </div>-->
<!--                    <div class="form-group col-md-1" style="margin-top: 2.0rem">-->
<!--                        <a class="btn btn-danger btn-sm white" id="removeCategoryItem"><i class="la la-trash-o"></i></a>-->
<!--                    </div>-->
<!--                </div>-->
<!--            @endif-->
<!--        </div>-->
<!--        <div class="card-footer">-->
<!--            <a class="btn btn-info btn-sm white" id="addVoucherItem" data-category="{{$categoryName}}" data-category-id="{{$categoryID}}"><i class="la la-plus-circle"></i></a>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<div class="card" id="{{$categoryName}}" data-parent="{{$parent}}">
    <div class="card-header card-head-inverse bg-secondary">
        <a data-action="collapse"><h4 class="card-title text-white">Local - Hotel Stay <span class="badge badge bg-white text-secondary badge-pill mr-2"></span></h4></a>
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
            @if(isset($voucher))
                @foreach($voucher->categoryItems($voucher->id,$categoryID) as $item)
                    {{--*/ $itemCount++ /*--}}
                    <div class="form-row">
                        <div class="form-group col-md-4 mb-2">
                            <label>Duration of Trip</label>
                            <div class='input-group'>
                                <input type='text' class="form-control datetimeHotelStay" name="date_range_{{$itemCount}}" value="{{date('m/d/Y H:i', strtotime($item->date_from)).' - '.date('m/d/Y H:i', strtotime($item->date_to))}}" required>
                                <div class="input-group-append">
                            <span class="input-group-text">
                              <span class="la la-calendar"></span>
                            </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-1 mb-2">
                            <label>Duration</label>
                            <input type="text" class="form-control" value="{{1+intval(date_diff(date_create(explode(' ',$item->date_to)[0]),date_create(explode(' ',$item->date_from)[0]))->format('%a'))}}" name="duration_{{$itemCount}}" id="duration_{{$itemCount}}" readonly>
                        </div>
                        <div class="form-group col-md-2 mb-2" hidden>
                            <label>Amount</label>
                            <input type="number" class="form-control" data-id='225' name="amount_{{$itemCount}}" value="{{$item->amount}}" id="amount_{{$itemCount}}">
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Amount paid</label>
                            <input type="text" class="form-control" data-id='225' name="amount_paid_{{$itemCount}}" value="{{$item->amount_paid}}" id="amount_paid_{{$itemCount}}" required>
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Amount to be paid</label>
                            <input type="text" class="form-control" value="{{$item->amount}}" name="amount_to_be_paid_{{$itemCount}}" id="amount_to_be_paid_{{$itemCount}}" readonly>
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
                    <div class="form-group col-md-4 mb-2">
                        <label>Duration of Trip</label>
                        <div class='input-group'>
                            <input type='text' class="form-control datetimeHotelStay" name="date_range_{{$itemCount}}" required>
                            <div class="input-group-append">
                            <span class="input-group-text">
                              <span class="la la-calendar"></span>
                            </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-1 mb-2">
                        <label>Duration</label>
                        <input type="text" class="form-control" value="0" name="duration_{{$itemCount}}" id="duration_{{$itemCount}}" readonly>
                    </div>
                    <div class="form-group col-md-2 mb-2" hidden>
                        <label>Amount</label>
                        <input type="number" class="form-control" data-id='225' name="amount_{{$itemCount}}" id="amount_{{$itemCount}}">
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <label>Amount paid</label>
                        <input type="text" class="form-control" data-id='225' name="amount_paid_{{$itemCount}}" id="amount_paid_{{$itemCount}}" required>
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <label>Amount to be paid</label>
                        <input type="text" class="form-control" value="0" name="amount_to_be_paid_{{$itemCount}}" id="amount_to_be_paid_{{$itemCount}}" readonly>
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
        </div>
        <div class="card-footer">
            <a class="btn btn-info btn-sm white" id="addVoucherItem" data-category="{{$categoryName}}" data-category-id="{{$categoryID}}"><i class="la la-plus-circle"></i></a>
        </div>
    </div>
</div>
