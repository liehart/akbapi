<body onload="window.print()">

<div style="max-width: 900px">
    <h1 class="font-bold text-xl">LAPORAN STOK BAHAN</h1>
    <p>Item menu: {{$menu->name}}</p>
    <p>Periode: <span class="font-bold">{{DateTime::createFromFormat('!m', $month)->format('F')}}</span>
        <span class="font-bold">{{$year}}</span>
    </p>

    <div style="margin-bottom: 20px">
        <table class="border" border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <thead>
            <tr>
                <th width="50">No</th>
                <th width="250">Tanggal</th>
                <th width="100">Unit</th>
                <th width="150">Incoming Stock</th>
                <th width="150">Remaining Stock</th>
                <th width="150">Waste Stock</th>
            </tr>
            </thead>
            <tbody>
            @foreach($stockMakanan as $key=>$data)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $data->date }}</td>
                    <td>{{ $data->unit }}</td>
                    <td>{{ $data->jumlah_masuk }}</td>
                    <td>{{ $data->jumlah_sisa }}</td>
                    <td>{{ $data->jumlah_dibuang }}</td>
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
