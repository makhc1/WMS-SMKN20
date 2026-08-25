<div class="p-6 bg-white text-gray-900 border rounded-lg print:border-none print:p-0">
    <div class="border-b pb-4 mb-6 text-center">
        <h2 class="text-2xl font-bold uppercase tracking-wider">SURAT JALAN / PICKING LIST</h2>
        <p class="text-gray-500 text-sm mt-1">Ref: #OUT-{{ str_pad($outbound->id, 5, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="flex justify-between mb-8">
        <div>
            <h4 class="font-semibold text-gray-700 text-sm uppercase">Tujuan Pengiriman:</h4>
            <p class="mt-1 font-medium">{{ $outbound->recipient ?? 'Penerima tidak diisi' }}</p>
            <p class="text-gray-600 text-sm">{{ $outbound->destination ?? 'Tujuan tidak diisi' }}</p>
        </div>
        <div class="text-right">
            <h4 class="font-semibold text-gray-700 text-sm uppercase">Tanggal Pengeluaran:</h4>
            <p class="mt-1 font-medium">{{ \Carbon\Carbon::parse($outbound->transaction_date)->format('d F Y') }}</p>
        </div>
    </div>

    <table class="w-full text-left border-collapse mb-8">
        <thead>
            <tr class="border-b-2 border-gray-300">
                <th class="py-2 font-semibold text-sm uppercase text-gray-600">Kode (SKU)</th>
                <th class="py-2 font-semibold text-sm uppercase text-gray-600">Nama Barang</th>
                <th class="py-2 font-semibold text-sm uppercase text-gray-600">Merk / Lokasi</th>
                <th class="py-2 text-right font-semibold text-sm uppercase text-gray-600">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b border-gray-200">
                <td class="py-3 font-mono text-sm">{{ $outbound->item->sku }}</td>
                <td class="py-3 font-medium">{{ $outbound->item->name }}</td>
                <td class="py-3 text-sm text-gray-600">
                    {{ $outbound->item->brand ?? '-' }}<br>
                    <span class="text-xs text-gray-400">Lok: {{ $outbound->item->location ?? '-' }}</span>
                </td>
                <td class="py-3 text-right font-bold text-lg">{{ $outbound->quantity }}</td>
            </tr>
        </tbody>
    </table>

    @if($outbound->notes)
    <div class="mb-8">
        <h4 class="font-semibold text-gray-700 text-sm uppercase mb-1">Catatan:</h4>
        <p class="text-sm text-gray-600 p-3 bg-gray-50 rounded">{{ $outbound->notes }}</p>
    </div>
    @endif

    <div class="flex justify-between mt-16 pt-8 border-t border-gray-200">
        <div class="text-center">
            <p class="mb-16 text-sm text-gray-600">Dikeluarkan Oleh,</p>
            <p class="border-t border-gray-400 pt-2 w-32 mx-auto font-medium text-sm">Petugas Gudang</p>
        </div>
        <div class="text-center">
            <p class="mb-16 text-sm text-gray-600">Diterima Oleh,</p>
            <p class="border-t border-gray-400 pt-2 w-32 mx-auto font-medium text-sm">Penerima</p>
        </div>
    </div>

    <div class="mt-8 text-center print:hidden">
        <button onclick="window.print()" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50" style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);">
            <svg class="fi-btn-icon h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
            </svg>
            <span class="fi-btn-label">Cetak Dokumen</span>
        </button>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .fi-modal-window {
            box-shadow: none !important;
            background: transparent !important;
        }
        .fi-modal-window * {
            visibility: visible;
        }
        button, .fi-modal-close-btn {
            display: none !important;
        }
        /* Reset absolute positioning for printing */
        .fi-modal-window {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    }
</style>
