<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, onMounted, ref, nextTick } from 'vue';
import JsBarcode from 'jsbarcode';

const props = defineProps({
    inbound: Object,
    inbounds: {
        type: Array,
        default: () => [],
    },
});

const barcodeList = computed(() =>
    (props.inbounds && props.inbounds.length ? props.inbounds : [props.inbound])
);

const barcodeRefs = ref([]);
const printReady = ref(false);

const renderBarcodes = () => {
    printReady.value = false;
    nextTick(() => {
        barcodeList.value.forEach((line, index) => {
            const el = barcodeRefs.value[index];
            if (el && line.item?.sku) {
                try {
                    JsBarcode(el, line.item.sku, {
                        format: "CODE128",
                        width: 2,
                        height: 90,
                        displayValue: true,
                        fontOptions: "bold",
                        textAlign: "center",
                        textPosition: "bottom",
                        textMargin: 6,
                        fontSize: 14,
                        background: "#ffffff",
                        lineColor: "#000000",
                        margin: 8,
                    });
                } catch (e) {
                    console.error('Barcode render failed:', e);
                }
            }
        });
        printReady.value = true;
    });
};

onMounted(() => {
    renderBarcodes();
});

const printAll = () => {
    window.print();
};
</script>

<template>
    <Head title="Detail Penerimaan & Barcode" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4 print:hidden">
                <Link :href="route('inbound.index')" class="text-gray-600 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detail Penerimaan & Barcode
                </h2>
            </div>
        </template>

        <div class="py-12 print:py-0">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <!-- Flash Message -->
                <div v-if="$page.props.flash?.message" class="mb-4 rounded-md bg-green-50 p-6 sm:p-8 border border-green-200 print:hidden">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ $page.props.flash.message }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg border border-gray-200 print:shadow-none print:border-none">
                    <div class="p-8">

                        <div class="text-center mb-10">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2 tracking-tight">LABEL BARCODE</h3>
                            <p class="text-gray-600 mb-6 print:hidden">
                                Tempelkan barcode berikut pada barang yang baru diterima ({{ barcodeList.length }} barang).
                            </p>

                            <!-- Barcodes -->
                            <div class="space-y-6">
                                <div
                                    v-for="(line, index) in barcodeList"
                                    :key="line.id"
                                    class="inline-block p-6 sm:p-8 border-2 border-dashed border-gray-300 rounded-lg print:border-none print:p-3 break-inside-avoid"
                                >
                                    <div class="text-sm font-bold text-gray-900 tracking-tight mb-4">
                                        {{ line.item?.name }} <span class="text-gray-400 font-normal">({{ line.quantity }} {{ line.item?.unit || 'Pcs' }})</span>
                                    </div>
                                    <svg :ref="el => barcodeRefs[index] = el" class="mx-auto"></svg>
                                </div>
                            </div>

                            <div class="mt-8 print:hidden">
                                <button
                                    @click="printAll"
                                    class="inline-flex items-center rounded-md bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                >
                                    <svg class="-ml-0.5 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                                    </svg>
                                    Cetak Semua ({{ barcodeList.length }} Label)
                                </button>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6 print:hidden">
                            <dl class="divide-y divide-gray-100">
                                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-6 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900">Receipt / No. Penerimaan</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ inbound.receipt_id || '-' }}</dd>
                                </div>
                                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-6 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900">Jumlah Barang Diterima</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ barcodeList.length }}</dd>
                                </div>
                                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-6 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900">Tanggal Penerimaan</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ inbound.transaction_date }}</dd>
                                </div>
                                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-6 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900">Supplier</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ inbound.supplier || '-' }}</dd>
                                </div>
                            </dl>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
