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
                                    <h2>Travel Order Approved - Arrangement Required</h2>
                                </header>
                                <p>Dear <strong>{{ $client->employee_name }}</strong>,</p>
                                <p>{{ $voucher->employee->employee_name }} has submitted a travel order.
                                    {{ $employee->employee_name }} has already approved this travel order. Tentative
                                    itinerary is as follows<br></p>
                                <br>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>No. of Days</th>
                                            <th>Accommodation Required</th>
                                            <th>Visa(s) Required</th>
                                            <th>Departure</th>
                                            <th>Arrival</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($voucher->voucherItems as $item)
                                            <tr>
                                                {{-- */ $voucher = new \App\Http\Controllers\VoucherController(); /* --}}
                                                {{-- */ $voucher->calculateDaysVoucherCopy($item->date_from, $item->date_to); /* --}}
                                                <td style="vertical-align: top">
                                                    <p>{{ date('d M, Y H:i', strtotime($item->date_from)) }}</p>
                                                </td>
                                                <td>
                                                    <p style="margin-bottom: 0px">
                                                        {{ date('d M, Y H:i', strtotime($item->date_to)) }}</p>
                                                </td>
                                                <td>
                                                    <p style="margin-bottom: 0px">
                                                        {{ date_diff(date_create($item->date_from), date_create($item->date_to))->format('%a') }}
                                                    </p>
                                                </td>
                                                <td>
                                                    <p style="margin-bottom: 0px">
                                                        {{ $item->hotel_required == 1 ? 'Yes' : 'No' }}</p>
                                                </td>
                                                <td>
                                                    @if ($item->visa_required)
                                                        <p style="margin-bottom: 0px">
                                                            {{ $item->visa_required == 1 ? 'Yes' : 'No' }}</p>
                                                    @else
                                                        <p style="margin-bottom: 0px">N/A</p>
                                                    @endif
                                                </td>
                                                @if ($item->from && $item->to)
                                                    <td>
                                                        <p style="margin-bottom: 0px">{{ $item->from }}</p>
                                                    </td>
                                                    <td>
                                                        <p style="margin-bottom: 0px">{{ $item->to }}</p>
                                                    </td>
                                                @else
                                                    @if ($lastCountry)
                                                        <td>
                                                            <p style="margin-bottom: 0px">{{ $lastLocation }}</p>
                                                        </td>
                                                    @else
                                                        <td>
                                                            <p style="margin-bottom: 0px">
                                                                {{ $item->voucher->employee->location }}</p>
                                                        </td>
                                                    @endif
                                                    <td>
                                                        <p style="margin-bottom: 0px">
                                                            {{ $item->country . ', ' . $item->city }}</p>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <hr>
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
