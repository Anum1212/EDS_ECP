<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Employee Reimbursement System</title>
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/email/email.css')}}" />
    <style>
        hr {
            display: block;
            height: 1px;
            border: 0;
            border-top: 1px solid #ccc;
            margin: 1em 0;
            padding: 0;
        }
        .table{
            width: 100%;
            max-width: 100%;
            border-spacing: 0;
            border-collapse: collapse;
            box-sizing: inherit;
            font-size: 15px;
            line-height: 1.5;
        }
        .table>thead>tr>th{
            vertical-align: bottom;
            border-bottom: 2px solid #ddd;
            padding: 8px;
            line-height: 1.42857143;
        }
        th{
            text-align: left;
        }
        .table>tbody>tr>td{
            padding: 8px;
            line-height: 1.42857143;
            vertical-align: top;
            border-top: 1px solid #ddd;
        }
    </style>
</head>

<body bgcolor="#FFFFFF">
<table class="body-wrap">
    <tr>
        <td></td>
        <td class="container" bgcolor="#FFFFFF">
            <div class="content">
                <table>
                    <tr>
                        <td>
                            <h2 style="background-color: #336699; padding: 13px; text-align: center; color: white">Documents Received</h2>
                            <p>Hello <strong>{{$client->nick_name}}</strong>,</p>
                            <p>Documents of your voucher # {{$voucher->id}} has been received in Accounts Department.<br></p>
                            <p>Voucher Details are as under:-<br></p>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Expense Description</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                {{--*/ $count = 1 /*--}}
                                @foreach($voucher->categories($voucher->id) as $category)
                                    <tr>
                                        <td style="vertical-align: top"><p>{{$count++}}</p></td>
                                        <td>
                                            <p style="margin-bottom: 0px">{{$category->category_name}}</p>
                                            @if($category->category_name == 'Misc')
                                                @foreach($voucher->voucherItems as $item)
                                                    @if($item->category->category_name == $category->category_name)
                                                        @if(isset($item->description))
                                                            <small>{!! $item->description !!}</small> <br>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td style="vertical-align: top"><small>PKR </small>{{number_format($voucher->categoryTotalAmount($voucher->id, $category->id), 0)}}</td>
                                    </tr>
                                @endforeach
                                {{--*/ $count = 1 /*--}}
                                </tbody>
                            </table>
                            <hr>
                            <p>Total: <strong><small>PKR </small>{{number_format($voucher->totalAmount($voucher->id), 0)}}</strong></p><br>
                            <p>Please login to <a href="https://ers.packagesgroup.com" style="text-decoration: none">Employee Reimbursement System</a> in order to view the complete status.</p><br>
                            <small>This is a system generated email. Please do not reply.</small>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
        <td></td>
    </tr>
</table>

</body>
</html>