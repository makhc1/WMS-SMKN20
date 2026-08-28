<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { PhPlus, PhCheck, PhEye, PhPackage } from '@phosphor-icons/vue';

const props = defineProps({
    pickingLists: Object,
});

const completePickingList = (id) => {
    if (confirm('Yakin ingin menyelesaikan Picking List ini?')) {
        router.post(route('picking-lists.complete', id));
    }
};

const getStatusBadge = (status) => {
    switch (status) {
        case 'completed':
            return 'bg-emerald-50 text-emerald-600';
        case 'pending':
            return 'bg-amber-50 text-amber-600';
        default:
            return 'bg-gray-50 text-gray-600';
    }
};
</script>

<template>
    <Head title="Picking List" />

    <AuthenticatedLayout>
        <template #header>
            Picking List
        </template>

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-6">
            <div></div>
            
            <Link :href="route('picking-lists.create')" class="group inline-flex items-center justify-between pl-6 pr-2 py-2 border border-transparent text-sm font-semibold rounded-full text-white bg-terracotta-600 hover:bg-terracotta-700 transition-all duration-700 ease-[cubic-bezier(0.32,0.72,0,1)] hover:scale-[0.98] w-full md:w-auto gap-6 shadow-[0_8px_30px_rgb(193,83,53,0.3)]">
                <span>Buat Picking List</span>
                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center transition-transform duration-700 ease-[cubic-bezier(0.32,0.72,0,1)] group-hover:scale-105 group-hover:translate-x-0.5">
                    <PhPlus class="w-4 h-4 text-white" weight="bold" />
                </div>
            </Link>
        </div>

        <div class="bg-white border border-black/10 rounded-[1.5rem] overflow-hidden">
            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-black/5">
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Kode</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Status</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Jumlah Barang</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Catatan</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Tanggal Dibuat</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            <tr v-for="pl in pickingLists.data" :key="pl.id" class="hover:bg-black/[0.01] transition-colors duration-300 group">
                                <td class="px-6 py-5 font-mono text-gray-600 font-bold text-xs">{{ pl.code }}</td>
                                <td class="px-6 py-5">
                                    <span :class="[
                                        'inline-flex items-center rounded-full px-3 py-1 text-xs font-bold tracking-tight',
                                        getStatusBadge(pl.status)
                                    ]">
                                        {{ pl.status === 'completed' ? 'Selesai' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-2">
                                        <PhPackage class="w-4 h-4 text-gray-400" />
                                        <span class="font-semibold text-black">{{ pl.items?.length || 0 }} barang</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-gray-600 max-w-[200px] truncate">{{ pl.notes || '-' }}</td>
                                <td class="px-6 py-5 text-gray-600">{{ new Date(pl.created_at).toLocaleDateString('id-ID') }}</td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link :href="route('picking-lists.show', pl.id)" class="p-2 text-gray-600 hover:text-black hover:bg-black/5 rounded-full transition-colors duration-300" title="Lihat Detail">
                                            <PhEye class="w-4 h-4" />
                                        </Link>
                                        <button v-if="pl.status === 'pending'" @click="completePickingList(pl.id)" class="p-2 text-gray-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-full transition-colors duration-300" title="Selesaikan">
                                            <PhCheck class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="pickingLists.data.length === 0">
                                <td colspan="6" class="px-8 py-24 text-center text-gray-600">
                                    <div role="status" class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                        <div class="w-16 h-16 rounded-full bg-black/5 flex items-center justify-center mb-4">
                                            <PhPackage class="w-8 h-8 text-gray-600" />
                                        </div>
                                        <h3 class="text-lg font-semibold text-black mb-1">Belum ada Picking List</h3>
                                        <p class="text-sm text-gray-600 mb-6">Buat Picking List baru untuk mulai mengambil barang dari gudang.</p>
                                        <Link :href="route('picking-lists.create')" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-semibold rounded-full text-white bg-black hover:bg-gray-800 transition-colors duration-300">
                                            Buat Picking List
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div v-if="pickingLists.links && pickingLists.links.length > 3" class="px-8 py-5 border-t border-black/5 flex items-center justify-between bg-black/[0.01]">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link v-if="pickingLists.prev_page_url" :href="pickingLists.prev_page_url" class="relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-black bg-white hover:bg-black/5 transition-colors">Previous</Link>
                        <span v-else class="relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-gray-600 bg-gray-50 cursor-not-allowed">Previous</span>
                        
                        <Link v-if="pickingLists.next_page_url" :href="pickingLists.next_page_url" class="ml-3 relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-black bg-white hover:bg-black/5 transition-colors">Next</Link>
                        <span v-else class="ml-3 relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-gray-600 bg-gray-50 cursor-not-allowed">Next</span>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-600">
                                Menampilkan <span class="font-medium text-black">{{ pickingLists.from }}</span> - <span class="font-medium text-black">{{ pickingLists.to }}</span> dari <span class="font-medium text-black">{{ pickingLists.total }}</span>
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-full shadow-sm bg-white border border-black/10 p-1 gap-1" aria-label="Pagination">
                                <template v-for="(link, i) in pickingLists.links" :key="i">
                                    <Link 
                                        v-if="link.url"
                                        :href="link.url" 
                                        :class="[
                                            link.active ? 'bg-terracotta-600 text-white font-semibold' : 'text-gray-600 hover:bg-black/5 hover:text-black',
                                            'relative inline-flex items-center px-6 py-4 text-sm rounded-full transition-all duration-300'
                                        ]"
                                    >{{ link.label }}</Link>
                                    <span v-else class="relative inline-flex items-center px-6 py-4 text-sm font-medium text-gray-400 cursor-not-allowed">{{ link.label }}</span>
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
