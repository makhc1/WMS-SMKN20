<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { PhArrowLeft, PhPrinter } from '@phosphor-icons/vue';
import QrcodeVue from 'qrcode.vue';

const props = defineProps({
    item: Object,
});

const printLabel = () => {
    window.print();
};
</script>

<template>
    <Head :title="`QR Code - ${item.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4 print:hidden">
                <Link :href="route('items.index')" class="text-gray-600 hover:text-gray-700 transition-colors">
                    <PhArrowLeft class="w-6 h-6" />
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 tracking-tight">
                    Label QR Code Barang
                </h2>
            </div>
        </template>

        <div class="flex justify-center py-6 md:py-12 px-6 lg:px-8 print:p-0">
            <!-- Printable Area -->
            <div class="printable-card bg-white p-8 md:p-12 rounded-[2.5rem] shadow-[0_8px_40px_rgb(0,0,0,0.06)] max-w-sm w-full border border-black/5 text-center relative print:shadow-none print:border-none print:p-6 sm:p-8">
                
                <h3 class="text-2xl font-bold tracking-tighter text-black mb-1 leading-tight">{{ item.name }}</h3>
                <p class="text-xs font-mono text-gray-600 mb-8">{{ item.sku }}</p>
                
                <div class="bg-black/[0.02] p-6 rounded-3xl inline-block border border-black/5 mb-8">
                    <qrcode-vue :value="item.sku" :size="200" level="H" class="mx-auto" />
                </div>
                
                <div class="flex flex-col gap-1 text-xs text-gray-600">
                    <p class="font-bold tracking-tight text-black">WMS SMKN 20</p>
                    <p>Scan barcode ini untuk proses Inbound &amp; Outbound</p>
                </div>

                <!-- Print Button (Hidden when printing) -->
                <button 
                    @click="printLabel" 
                    class="mt-10 group w-full inline-flex items-center justify-center px-6 py-6 border border-transparent rounded-full text-sm font-semibold text-white bg-terracotta-600 hover:bg-terracotta-700 transition-all duration-700 ease-[cubic-bezier(0.32,0.72,0,1)] shadow-[0_8px_30px_rgb(193,83,53,0.3)] hover:scale-[0.98] print:hidden gap-3"
                >
                    <PhPrinter class="w-5 h-5" />
                    Cetak Label Sekarang
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@media print {
    body {
        background-color: white !important;
    }
    
    /* Hide everything else */
    header, nav, aside, .print\:hidden {
        display: none !important;
    }

    /* Override layout padding for print */
    main {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Make the card the only thing that prints */
    .printable-card {
        box-shadow: none !important;
        border: 2px dashed #e5e7eb !important; /* Cut guide */
        margin: 0 auto !important;
        border-radius: 1rem !important;
    }
}
</style>
