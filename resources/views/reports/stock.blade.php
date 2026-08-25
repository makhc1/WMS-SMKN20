<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Gudang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .danger { color: #d9534f; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN STOK GUDANG SMKN 20</h1>
        <p>Tanggal Cetak: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="15%">SKU</th>
                <th width="40%">Nama Barang</th>
                <th width="15%" class="text-center">Stok Min</th>
                <th width="15%" class="text-center">Stok Tersedia</th>
                <th width="10%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->sku }}</td>
                <td>{{ $item->name }}</td>
                <td class="text-center">{{ $item->low_stock_threshold }}</td>
                <td class="text-center {{ $item->quantity <= $item->low_stock_threshold ? 'danger' : '' }}">{{ $item->quantity }}</td>
                <td class="text-center {{ $item->quantity <= $item->low_stock_threshold ? 'danger' : '' }}">
                    {{ $item->quantity <= $item->low_stock_threshold ? 'Menipis' : 'Aman' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
