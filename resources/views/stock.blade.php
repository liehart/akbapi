<body onload="window.print()">
<div>
    <h1 class="font-bold text-xl">LAPORAN STOK BAHAN</h1>
    <p>Item menu: Semua</p>
    <p>Periode: Custom (
        <span class="font-bold">{{\Carbon\Carbon::parse($start)->format('d M Y')}}</span>
        sampai
        <span class="font-bold">{{\Carbon\Carbon::parse($end)->format('d M Y')}}</span>
        )
    </p>

    <hr/>

    <div>
        <h2>Makanan utama</h2>
        <table class="border" border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <thead>
            <tr>
                <th width="50">No</th>
                <th width="250">Item Menu</th>
                <th width="100">Unit</th width="100">
                <th width="150">Incoming Stock</th>
                <th width="150">Remaining Stock</th>
                <th width="150">Waste Stock</th>
            </tr>
            </thead>
            <tbody>
            @foreach($stockMakanan as $key=>$data)
                @if($data->menu->menu_type == 'main')
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $data->menu->name }}</td>
                        <td>{{ $data->unit }}</td>
                        <td>{{ $data->in->sum('quantity') }}</td>
                        <td>{{ $data->in->sum('quantity') - $data->out->sum('quantity') }}</td>
                        <td>{{ $data->out->where('category', '=', 'waste')->sum('quantity') }}</td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>

    <div>
        <h2>Minuman</h2>
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <thead>
            <tr>
                <th width="50">No</th>
                <th width="250">Item Menu</th>
                <th width="100">Unit</th>
                <th width="150">Incoming Stock</th>
                <th width="150">Remaining Stock</th>
                <th width="150">Waste Stock</th>
            </tr>
            </thead>
            <tbody>
            @foreach($stockMakanan as $key=>$data)
                @if($data->menu->menu_type == 'drink')
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $data->menu->name }}</td>
                        <td>{{ $data->unit }}</td>
                        <td>{{ $data->in->sum('quantity') }}</td>
                        <td>{{ $data->in->sum('quantity') - $data->out->sum('quantity') }}</td>
                        <td>{{ $data->out->where('category', '=', 'waste')->sum('quantity') }}</td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>

    <div>
        <h2>Side Dish</h2>
        <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <thead>
            <tr>
                <th width="50">No</th>
                <th width="250">Item Menu</th>
                <th width="100">Unit</th>
                <th width="150">Incoming Stock</th>
                <th width="150">Remaining Stock</th>
                <th width="150">Waste Stock</th>
            </tr>
            </thead>
            <tbody>
            @foreach($stockMakanan as $key=>$data)
                @if($data->menu->menu_type == 'side_dish')
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $data->menu->name }}</td>
                        <td>{{ $data->unit }}</td>
                        <td>{{ $data->in->sum('quantity') }}</td>
                        <td>{{ $data->in->sum('quantity') - $data->out->sum('quantity') }}</td>
                        <td>{{ $data->out->where('category', '=', 'waste')->sum('quantity') }}</td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px">
        <hr/>

        <p>Printed at: {{ \Carbon\Carbon::now() }}</p>
        <p>Printed by: Ops. Manager Account</p>
    </div>
</div>


