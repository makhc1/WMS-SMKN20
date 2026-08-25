<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Mutasi Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 11px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .inbound { color: #0076a3; font-weight: bold; }
        .outbound { color: #d9534f; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN MUTASI BARANG GUDANG SMKN 20</h1>
        <p>Periode: {{ $start_date }} s/d {{ $end_date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="12%">Tanggal</th>
                <th width="8%" class="text-center">Tipe</th>
                <th width="20%">Nama Barang</th>
                <th width="25%">Pihak Terkait (Asal/Tujuan)</th>
                <th width="20%">Catatan</th>
                <th width="10%" class="text-center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mutations as $index => $tx)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($tx->transaction_date)->format('d/m/Y') }}</td>
                <td class="text-center {{ $tx->type == 'inbound' ? 'inbound' : 'outbound' }}">
                    {{ strtoupper($tx->type) }}
                </td>
                <td>{{ $tx->item->name }}</td>
                <td>{{ $tx->party }}</td>
                <td>{{ $tx->notes ?? '-' }}</td>
                <td class="text-center {{ $tx->type == 'inbound' ? 'inbound' : 'outbound' }}">
                    {{ $tx->type == 'inbound' ? '+' : '-' }}{{ $tx->quantity }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
