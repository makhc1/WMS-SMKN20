<div class="text-center p-4">
    <div class="mb-4">
        <h3 class="text-lg font-bold">{{ $item->name }}</h3>
        <p class="text-sm text-gray-500">{{ $item->sku }}</p>
    </div>
    
    <div class="inline-block p-4 border-2 border-dashed border-gray-300 rounded-lg">
        {!! $barcode !!}
        <div class="mt-2 text-sm font-mono font-bold">{{ $item->sku }}</div>
    </div>
    
    <div class="mt-6">
        <button onclick="window.print()" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50" style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);">
            <svg class="fi-btn-icon h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
            </svg>
            <span class="fi-btn-label">Cetak Barcode</span>
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
    }
</style>
