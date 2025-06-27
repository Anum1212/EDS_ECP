<div class="card">
    <div class="card-header card-head-inverse bg-primary">
        <a data-action="collapse"><h4 class="card-title text-white">{{$category->category_name}} <span class="badge badge bg-white text-secondary badge-pill mr-2"><small>PKR </small>{{number_format($voucher->categoryTotalAmount($voucher->id, $category->id), 0)}}</span></h4></a>
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
            @if(count($voucher->categoryItems($voucher->id, $category->id)) > 0)
                <table class="table table-white-space table-bordered display no-wrap table-middle">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Mode</th>
                        <th>Amount (PKR)</th>
                        <th>Receipt Provided</th>
                        @if(count($voucher->voucherStatus) == 0)
                            <th>Delete</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($voucher->categoryItems($voucher->id, $category->id) as $item)
                        <tr class="taskrow">
                            <td>{{date('M d, Y', strtotime($item->date_from))}}</td>
                            <td>{{$item->from}}</td>
                            <td>{{$item->to}}</td>
                            <td>{{$item->travel_mode}}</td>
                            <td>{{number_format($item->amount,0)}}</td>
                            <td>{{$item->receipt_copy == 1 ? 'Yes':'No'}}</td>
                            @if(count($voucher->voucherStatus) == 0)
                                <td><a href="{{URL::to('voucher/item/delete'.'/'.$item->id)}}" class="danger"><i class="la la-trash"></i></a></td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
