<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $order->transaction->transaction_sn }} - Receipt</title>
</head>

<body>
<div class="container" style="max-width: 500px;">
    <h2 class="text-center" style="padding-bottom: 0; margin-bottom: 0">Atma Korean BBQ</h2>
    <h4 class="text-center" style="padding-top: 0; margin-top: 0">Fun Place To Grill</h4>
    <hr/>

    <table>
        <tbody>
        <tr>
            <td width="75px">Receipt #</td>
            <td width="200px">{{ $order->transaction->transaction_sn }}</td>
            <td width="75px">Date</td>
            <td width="100px">{{ Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</td>
        </tr>
        <tr>
            <td>Waiter</td>
            <td>{{ $order->waiter->name }}</td>
            <td>Time</td>
            <td>{{ Carbon\Carbon::parse($order->order_date)->format('H:i') }}</td>
        </tr>
        </tbody>
    </table>

    <hr/>

    <table>
        <tbody>
        <tr>
            <td width="75px">Table #</td>
            <td width="200px">{{ 'Meja ' . $order->reservation->table_number }}</td>
            <td width="75px">Customer</td>
            <td width="100px">{{ $order->reservation->customer->name }}</td>
        </tr>
        </tbody>
    </table>

    <hr/>

    <table>
        <thead>
        <tr class="table-danger">
            <th scope="col">Qty</th>
            <th scope="col">Item Menu</th>
            <th scope="col">Harga</th>
            <th scope="col">Sub Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->details ?? '' as $data)
            <tr>
                <th scope="row">{{ $data->quantity }}</th>
                <td width="300px">{{ $data->menu->name }}</td>
                <td width="100px">{{ $data->menu->price }}</td>
                <td width="100px">{{ $data->menu->price * $data->quantity }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr/>
    <table>
        <tbody>
        <tr>
            <th scope="row"></th>
            <td width="300px"></td>
            <td width="100px">Sub Total</td>
            <td width="100px">{{ $order->transaction->subtotal }}</td>
        </tr>
        <tr>
            <th scope="row"></th>
            <td width="300px"></td>
            <td width="100px">Service 5%</td>
            <td width="100px">{{ $order->transaction->service }}</td>
        </tr>
        <tr>
            <th scope="row"></th>
            <td width="300px"></td>
            <td width="100px">Tax 10%</td>
            <td width="100px">{{ $order->transaction->tax }}</td>
        </tr>
        <tr>
            <th scope="row"></th>
            <td width="300px"></td>
            <td width="100px">Total</td>
            <td width="100px">{{ $order->transaction->grand_total }}</td>
        </tr>
        </tbody>
    </table>
    <hr/>
    <div>
        <div>
            Total Item: {{ $order->total_item }}
        </div>
        <div>
            Total Menu: {{ $order->total_menu }}
        </div>
        <div>
            Printed at: {{ \Carbon\Carbon::now() }}
        </div>
        <div>
            Cashier: {{ $order->transaction->cashier->name }}
        </div>
    </div>
    <hr/>
</div>
<a onclick="print()">
    <button>
        Print
    </button>
</a>

</body>

</html>
