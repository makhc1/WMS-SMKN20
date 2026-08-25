<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { PhFilePdf, PhCalendarBlank, PhDownloadSimple } from '@phosphor-icons/vue';

const form = useForm({
    start_date: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
    end_date: new Date().toISOString().split('T')[0],
});

const downloadStock = () => {
    // We must use standard form submission for file downloads, not Inertia XHR
    const formElement = document.createElement('form');
    formElement.method = 'POST';
    formElement.action = route('reports.stock.pdf');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = csrfToken;
        formElement.appendChild(tokenInput);
    }
    
    document.body.appendChild(formElement);
    formElement.submit();
    document.body.removeChild(formElement);
};

const downloadMutations = () => {
    const formElement = document.createElement('form');
    formElement.method = 'POST';
    formElement.action = route('reports.mutations.pdf');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = csrfToken;
        formElement.appendChild(tokenInput);
    }
    
    const startInput = document.createElement('input');
    startInput.type = 'hidden';
    startInput.name = 'start_date';
    startInput.value = form.start_date;
    
    const endInput = document.createElement('input');
    endInput.type = 'hidden';
    endInput.name = 'end_date';
    endInput.value = form.end_date;
    
    formElement.appendChild(startInput);
    formElement.appendChild(endInput);
    
    document.body.appendChild(formElement);
    formElement.submit();
    document.body.removeChild(formElement);
};
</script>

<template>
    <Head title="Pusat Laporan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-bold tracking-tighter text-black">
                Pusat Laporan &amp; Export
            </h2>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-6xl">
            <!-- Laporan Stok Gudang -->
            <div class="bg-white border-r border-black/10 group hover:-translate-y-1 transition-transform duration-500">
                <div class="bg-transparent  p-8 md:p-12 h-full flex flex-col justify-between">
                    <div>
                        <div class="w-16 h-16 rounded-3xl bg-terracotta-50 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                            <PhFilePdf class="w-8 h-8 text-terracotta-600" />
                        </div>
                        <h3 class="text-2xl font-bold tracking-tighter text-black mb-2">Laporan Stok Sisa</h3>
                        <p class="text-sm text-gray-600 leading-relaxed mb-8">
                            Download rekapitulasi jumlah stok seluruh barang di gudang saat ini. Dilengkapi dengan indikator batas minimal stok.
                        </p>
                    </div>
                    
                    <button 
                        @click="downloadStock"
                        class="w-full inline-flex items-center justify-center px-6 py-6 border border-transparent rounded-full text-sm font-semibold text-white bg-black hover:bg-gray-800 transition-all duration-700 ease-[cubic-bezier(0.32,0.72,0,1)] shadow-[0_8px_30px_rgb(0,0,0,0.2)] hover:scale-[0.98] gap-3"
                    >
                        <PhDownloadSimple class="w-5 h-5" />
                        Download PDF Stok
                    </button>
                </div>
            </div>

            <!-- Laporan Mutasi Barang -->
            <div class="bg-white border-r border-black/10 group hover:-translate-y-1 transition-transform duration-500">
                <div class="bg-transparent  p-8 md:p-12 h-full flex flex-col justify-between">
                    <div>
                        <div class="w-16 h-16 rounded-3xl bg-blue-50 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                            <PhCalendarBlank class="w-8 h-8 text-blue-600" />
                        </div>
                        <h3 class="text-2xl font-bold tracking-tighter text-black mb-2">Laporan Mutasi Inbound/Outbound</h3>
                        <p class="text-sm text-gray-600 leading-relaxed mb-6">
                            Download riwayat masuk dan keluarnya barang beserta informasi supplier dan peminjam berdasarkan rentang tanggal.
                        </p>

                        <!-- Date Filters -->
                        <div class="grid grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-xs font-bold tracking-tight text-gray-900 mb-2 uppercase">Dari Tanggal</label>
                                <input 
                                    type="date" 
                                    v-model="form.start_date"
                                    class="block w-full px-6 py-4 border border-black/10 rounded-2xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold tracking-tight text-gray-900 mb-2 uppercase">Sampai Tanggal</label>
                                <input 
                                    type="date" 
                                    v-model="form.end_date"
                                    class="block w-full px-6 py-4 border border-black/10 rounded-2xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all"
                                />
                            </div>
                        </div>
                    </div>
                    
                    <button 
                        @click="downloadMutations"
                        class="w-full inline-flex items-center justify-center px-6 py-6 border border-transparent rounded-full text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-all duration-700 ease-[cubic-bezier(0.32,0.72,0,1)] shadow-[0_8px_30px_rgb(37,99,235,0.3)] hover:scale-[0.98] gap-3"
                    >
                        <PhDownloadSimple class="w-5 h-5" />
                        Download PDF Mutasi
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
