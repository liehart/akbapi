<body onload="window.print()">

<div style="max-width: 900px">
    <h1 style="margin: 0">LAPORAN PENDAPATAN TAHUNAN</h1>
    <p style="margin: 0">Tahun: {{$start}} - {{$end}}</p>
    <hr/>

    <div style="margin-bottom: 20px; margin-top: 20px">
        <table class="border" border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <thead>
            <tr>
                <th width="50">No</th>
                <th width="250">Tahun</th>
                <th width="100">Makanan</th>
                <th width="150">Side Dish</th>
                <th width="150">Minuman</th>
                <th width="150">Total Pendapatan</th>
            </tr>
            </thead>
            <tbody>
            @foreach($pengeluaran as $key=>$data)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $data->tahun }}</td>
                    <td>{{ number_format($data->jumlah_makanan) }}</td>
                    <td>{{ number_format($data->jumlah_side_dish) }}</td>
                    <td>{{ number_format($data->jumlah_minuman) }}</td>
                    <td>{{ number_format($data->jumlah_terjual) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<div style="margin-top: 20px">
    <hr/>

    <p>Printed at: {{ \Carbon\Carbon::now() }}</p>
    <p>Printed by: Ops. Manager Account</p>
</div>
</body>
