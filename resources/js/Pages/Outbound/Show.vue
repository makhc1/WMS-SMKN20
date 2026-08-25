<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { PhPrinter, PhArrowLeft } from '@phosphor-icons/vue';

const props = defineProps({
    outbound: Object,
});

const printDocument = () => {
    window.print();
};
</script>

<template>
    <Head title="Surat Jalan & Picking List" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4 print:hidden">
                <Link :href="route('outbound.index')" class="text-gray-600 hover:text-gray-700">
                    <PhArrowLeft class="w-6 h-6" />
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Surat Jalan & Picking List
                </h2>
            </div>
        </template>

        <div class="py-6 sm:py-12 print:py-0">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <!-- Flash Message -->
                <div v-if="$page.props.flash?.message" class="mb-6 rounded-xl bg-emerald-50 p-6 sm:p-8 border border-emerald-200 print:hidden shadow-sm">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-emerald-800">
                                {{ $page.props.flash.message }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-200 print:shadow-none print:border-none overflow-hidden" id="printable-area">
                    <div class="p-8 sm:p-12">
                        
                        <!-- Header Surat Jalan -->
                        <div class="flex justify-between items-start border-b-2 border-gray-900 pb-6 mb-8 printable-visible">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">SURAT JALAN / PENGELUARAN</h1>
                                <p class="text-gray-600 mt-1">WMS SMKN 20</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">No. Transaksi: OUT-{{ outbound.id.toString().padStart(5, '0') }}</p>
                                <p class="text-sm text-gray-600 mt-1">Tanggal: {{ new Date(outbound.transaction_date).toLocaleDateString('id-ID') }}</p>
                            </div>
                        </div>

                        <!-- Info Pengirim & Penerima -->
                        <div class="grid grid-cols-2 gap-12 mb-10 printable-visible">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-2">Dari (Gudang)</h3>
                                <p class="text-gray-700">SMKN 20 Jakarta</p>
                                <p class="text-gray-600 text-sm">Bagian Logistik / Sarpras</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-2">Tujuan / Penerima</h3>
                                <p class="text-gray-700 font-medium">{{ outbound.recipient || 'Tidak disebutkan' }}</p>
                                <p class="text-gray-600 text-sm">{{ outbound.destination || '-' }}</p>
                            </div>
                        </div>

                        <!-- Table Barang -->
                        <div class="mb-12 printable-visible">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b-2 border-gray-900">
                                        <th class="py-3 px-6 lg:px-8 text-sm font-bold text-gray-900">No</th>
                                        <th class="py-3 px-6 lg:px-8 text-sm font-bold text-gray-900">SKU / Kode Barang</th>
                                        <th class="py-3 px-6 lg:px-8 text-sm font-bold text-gray-900">Nama Barang</th>
                                        <th class="py-3 px-6 lg:px-8 text-sm font-bold text-gray-900">Lokasi Ambil (Picking)</th>
                                        <th class="py-3 px-6 lg:px-8 text-sm font-bold text-gray-900 text-right">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-gray-200">
                                        <td class="py-6 px-6 lg:px-8 text-sm text-gray-900">1</td>
                                        <td class="py-6 px-6 lg:px-8 text-sm font-mono text-gray-700">{{ outbound.item.sku }}</td>
                                        <td class="py-6 px-6 lg:px-8 text-sm font-medium text-gray-900">{{ outbound.item.name }}</td>
                                        <td class="py-6 px-6 lg:px-8 text-sm text-gray-700">{{ outbound.item.location || '-' }}</td>
                                        <td class="py-6 px-6 lg:px-8 text-sm font-bold text-gray-900 text-right">{{ outbound.quantity }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Catatan -->
                        <div class="mb-16 printable-visible" v-if="outbound.notes">
                            <h3 class="text-sm font-bold text-gray-900 mb-2">Catatan:</h3>
                            <p class="text-gray-700 text-sm italic">{{ outbound.notes }}</p>
                        </div>

                        <!-- Tanda Tangan -->
                        <div class="grid grid-cols-3 gap-8 text-center mt-20 printable-visible">
                            <div>
                                <p class="text-sm text-gray-700 mb-20">Yang Menyerahkan,</p>
                                <p class="text-sm font-bold text-gray-900 border-b border-gray-400 inline-block px-8 pb-1">Petugas Gudang</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-700 mb-20">Mengetahui,</p>
                                <p class="text-sm font-bold text-gray-900 border-b border-gray-400 inline-block px-8 pb-1">Kepala Sarpras</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-700 mb-20">Penerima,</p>
                                <p class="text-sm font-bold text-gray-900 border-b border-gray-400 inline-block px-8 pb-1">{{ outbound.recipient || '......................' }}</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-12 flex justify-end print:hidden border-t border-gray-100 pt-8">
                            <button 
                                @click="printDocument"
                                class="inline-flex items-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors"
                            >
                                <PhPrinter class="-ml-0.5 mr-2 h-5 w-5" weight="bold" />
                                Cetak Surat Jalan & Picking List
                            </button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

</template>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printable-area, #printable-area * {
            visibility: visible;
        }
        #printable-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 20mm;
        }
        .print\:hidden {
            display: none !important;
        }
        @page {
            size: A4;
            margin: 0;
        }
    }
</style>
