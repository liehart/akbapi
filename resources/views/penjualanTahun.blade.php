<body onload="window.print()">
<div style="max-width: 900px">
    <h1 style="margin: 0">LAPORAN PENJUALAN ITEM MENU TAHUNAN</h1>
    <p style="margin: 0">Tahun: {{$year}}</p>
    <p style="margin: 0">Bulan: All</p>
    <hr/>

    <div style="margin-bottom: 20px">
        <h2>Makanan utama</h2>
        <table class="border" border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <thead>
            <tr>
                <th width="50">No</th>
                <th width="250">Item Menu</th>
                <th width="100">Unit</th>
                <th width="150">Penjualan Harian Tertinggi</th>
                <th width="150">Total Penjualan</th>
            </tr>
            </thead>
            <tbody>
            @foreach($pengeluaran as $key=>$data)
                @if($data->menu_type == 'main')
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $data->name }}</td>
                        <td>{{ $data->unit }}</td>
                        <td>{{ $data->jumlah_penjualan_tertinggi }}</td>
                        <td>{{ $data->jumlah_penjualan }}</td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-bottom: 20px">
        <h2>Minuman</h2>
        <table class="border" border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <thead>
            <tr>
                <th width="50">No</th>
                <th width="250">Item Menu</th>
                <th width="100">Unit</th>
                <th width="150">Penjualan Harian Tertinggi</th>
                <th width="150">Total Penjualan</th>
            </tr>
            </thead>
            <tbody>
            @foreach($pengeluaran as $key=>$data)
                @if($data->menu_type == 'drink')
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $data->name }}</td>
                        <td>{{ $data->unit }}</td>
                        <td>{{ $data->jumlah_penjualan_tertinggi }}</td>
                        <td>{{ $data->jumlah_penjualan }}</td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-bottom: 20px">
        <h2>Makanan utama</h2>
        <table class="border" border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <thead>
            <tr>
                <th width="50">No</th>
                <th width="250">Item Menu</th>
                <th width="100">Unit</th>
                <th width="150">Penjualan Harian Tertinggi</th>
                <th width="150">Total Penjualan</th>
            </tr>
            </thead>
            <tbody>
            @foreach($pengeluaran as $key=>$data)
                @if($data->menu_type == 'side_dish')
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $data->name }}</td>
                        <td>{{ $data->unit }}</td>
                        <td>{{ $data->jumlah_penjualan_tertinggi }}</td>
                        <td>{{ $data->jumlah_penjualan }}</td>
                    </tr>
                @endif
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
