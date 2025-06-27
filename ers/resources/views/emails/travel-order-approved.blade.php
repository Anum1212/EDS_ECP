<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Employee Claim Portal</title>
    <style>
        body {
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
            width: 100% !important;
            height: 100%;
            background-color: #FFFFFF;
        }

        .container {
            background-color: #FFFFFF;
        }

        header,
        footer {
            background-color: #4D4A4F;
            border-bottom: 10px solid #F0AB00;
            padding: 13px 13px 0px 13px;
            text-align: center;
            color: white
        }

        footer a{
            color: #F0AB00;
        }

        .table {
            width: 100%;
            max-width: 100%;
            border-spacing: 0;
            border-collapse: collapse;
            box-sizing: inherit;
            font-size: 15px;
            line-height: 1.5;
        }

        .table>thead>tr>th {
            vertical-align: bottom;
            border-bottom: 2px solid #ddd;
            padding: 8px;
            line-height: 1.42857143;
        }

        th {
            text-align: left;
        }

        .table>tbody>tr>td {
            padding: 8px;
            line-height: 1.42857143;
            vertical-align: top;
            border-top: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <table class="body-wrap">
        <tr>
            <td></td>
            <td class="container">
                <div class="content">
                    <table>
                        <tr>
                            <td>
                                <header>
                                <h2>Travel Order Approved</h2>
                            </header>
                                <p>Hello <strong>{{ $client->nick_name }}</strong>,</p>
                                <p>{{ $employee->employee_name }} has approved your travel order # {{ $voucher->id }}
                                    in Employee Claim Portal.<br></p>
                                <p>Travel Order Details are as under:-<br></p>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Expense Description</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- */ $count = 1 /* --}}
                                        @foreach ($voucher->categories($voucher->id) as $category)
                                            <tr>
                                                <td style="vertical-align: top">
                                                    <p>{{ $count++ }}</p>
                                                </td>
                                                <td>
                                                    <p style="margin-bottom: 0px">{{ $category->category_name }}</p>
                                                    @if ($category->category_name == 'Misc')
                                                        @foreach ($voucher->voucherItems as $item)
                                                            @if ($item->category->category_name == $category->category_name)
                                                                @if (isset($item->description))
                                                                    <small>{!! $item->description !!}</small> <br>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </td>
                                                @if ($voucher->totalAmount($voucher->id) == 0 && $voucher->totalAmountForex($voucher->id) > 0)
                                                    <td style="vertical-align: top"><small>USD
                                                        </small>{{ number_format($voucher->categoryTotalAmountForex($voucher->id, $category->id), 0) }}
                                                    </td>
                                                @else
                                                    <td style="vertical-align: top"><small>PKR
                                                        </small>{{ number_format($voucher->categoryTotalAmount($voucher->id, $category->id), 0) }}
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                        {{-- */ $count = 1 /* --}}
                                    </tbody>
                                </table>
                                <hr>
                                @if ($voucher->totalAmount($voucher->id) == 0 && $voucher->totalAmountForex($voucher->id) > 0)
                                    <p>Total: <strong><small>USD
                                            </small>{{ number_format($voucher->totalAmountForex($voucher->id), 0) }}</strong>
                                    </p><br>
                                @else
                                    <p>Total: <strong><small>PKR
                                            </small>{{ number_format($voucher->totalAmount($voucher->id), 0) }}</strong>
                                    </p><br>
                                @endif
                                <footer>
                                    <p>Please login to <a href="https://hcm44.packagesgroup.com"
                                            style="text-decoration: none">Employee Claim Portal</a> in order to view the
                                        details.</p><br>
                                    </footer>
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
