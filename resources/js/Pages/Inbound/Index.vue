<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { PhPlus, PhEye, PhTrash } from '@phosphor-icons/vue';

const props = defineProps({
    inbounds: Object
});
</script>

<template>
    <Head title="Penerimaan Barang" />

    <AuthenticatedLayout>
        <template #header>
            Penerimaan Barang (Inbound)
        </template>

        <div class="flex justify-end mb-8">
            <Link :href="route('inbound.create')" class="group inline-flex items-center justify-between pl-6 pr-2 py-2 border border-transparent text-sm font-semibold rounded-full text-white bg-terracotta-600 hover:bg-terracotta-700 transition-all duration-700 ease-[cubic-bezier(0.32,0.72,0,1)] hover:scale-[0.98] shadow-[0_8px_30px_rgb(193,83,53,0.3)] gap-6 w-full md:w-auto">
                <span>Input Barang Masuk</span>
                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center transition-transform duration-700 ease-[cubic-bezier(0.32,0.72,0,1)] group-hover:scale-105 group-hover:translate-x-0.5">
                    <PhPlus class="w-4 h-4 text-white" weight="bold" />
                </div>
            </Link>
        </div>

        <div class="bg-white border border-black/10 rounded-[1.5rem] overflow-hidden">
            <div class=" overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-black/5">
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Receipt ID</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Tanggal</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Barang</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Supplier</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Status</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Kondisi</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs text-right">Qty</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs text-right"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            <tr v-for="tx in inbounds.data" :key="tx.id" class="hover:bg-black/[0.01] transition-colors duration-300 group">
                                <td class="px-6 py-5 font-mono text-gray-600 text-xs">{{ tx.receipt_id || 'N/A' }}</td>
                                <td class="px-6 py-5 text-gray-600 font-medium">{{ new Date(tx.transaction_date).toLocaleDateString('id-ID') }}</td>
                                <td class="px-6 py-5">
                                    <p class="font-semibold text-black">{{ tx.item?.name }}</p>
                                    <p class="font-mono text-gray-600 text-[10px]">{{ tx.item?.sku }}</p>
                                </td>
                                <td class="px-6 py-5 text-gray-600">{{ tx.supplier || '-' }}</td>
                                <td class="px-6 py-5">
                                    <span v-if="tx.status === 'completed'" class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold tracking-tight bg-emerald-50 text-emerald-600">
                                        Completed
                                    </span>
                                    <span v-else class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold tracking-tight bg-amber-50 text-amber-600">
                                        Pending
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <span :class="[
                                        'inline-flex items-center rounded-full px-3 py-1 text-xs font-bold tracking-tight',
                                        tx.condition === 'Damaged' ? 'bg-red-50 text-red-600' : 'bg-gray-100 text-gray-600'
                                    ]">
                                        {{ tx.condition || 'Good' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <span class="inline-flex items-center rounded-full bg-black/5 px-3 py-1 text-xs font-bold tracking-tight text-black">
                                        +{{ tx.quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end opacity-0 group-hover:opacity-100 transition-opacity duration-300 gap-2">
                                        <Link v-if="tx.status === 'pending'" :href="route('inbound.complete', tx.id)" method="post" as="button" type="button" class="px-3 py-1 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-full transition-colors" preserve-scroll>
                                            Selesaikan
                                        </Link>
                                        <Link :href="route('inbound.show', tx.id)" class="p-2 text-gray-600 hover:text-black hover:bg-black/5 rounded-full transition-colors duration-300" aria-label="Lihat Detail">
                                            <PhEye class="w-4 h-4 inline" aria-hidden="true" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="inbounds.data.length === 0">
                                <td colspan="8" class="px-8 py-24 text-center text-gray-600">
                                    <div role="status" class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                        <div class="w-16 h-16 rounded-full bg-black/5 flex items-center justify-center mb-4">
                                            <PhPlus class="w-8 h-8 text-gray-600" />
                                        </div>
                                        <h3 class="text-lg font-semibold text-black mb-1">Belum ada transaksi masuk</h3>
                                        <p class="text-sm text-gray-600 mb-6">Catat penerimaan barang pertama Anda untuk menambah stok ke dalam gudang.</p>
                                        <Link :href="route('inbound.create')" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-semibold rounded-full text-white bg-black hover:bg-gray-800 transition-colors duration-300">
                                            Input Barang Masuk
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div v-if="inbounds.links && inbounds.links.length > 3" class="px-8 py-5 border-t border-black/5 flex items-center justify-between bg-black/[0.01]">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link v-if="inbounds.prev_page_url" :href="inbounds.prev_page_url" class="relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-black bg-white hover:bg-black/5 transition-colors">Previous</Link>
                        <span v-else class="relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-gray-600 bg-gray-50 cursor-not-allowed">Previous</span>
                        
                        <Link v-if="inbounds.next_page_url" :href="inbounds.next_page_url" class="ml-3 relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-black bg-white hover:bg-black/5 transition-colors">Next</Link>
                        <span v-else class="ml-3 relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-gray-600 bg-gray-50 cursor-not-allowed">Next</span>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-600">
                                Menampilkan <span class="font-medium text-black">{{ inbounds.from }}</span> - <span class="font-medium text-black">{{ inbounds.to }}</span> dari <span class="font-medium text-black">{{ inbounds.total }}</span>
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-full shadow-sm bg-white border border-black/10 p-1 gap-1" aria-label="Pagination">
                                <template v-for="(link, i) in inbounds.links" :key="i">
                                    <Link 
                                        v-if="link.url"
                                        :href="link.url" 
                                        v-html="link.label"
                                        :class="[
                                            link.active ? 'bg-terracotta-600 text-white font-semibold' : 'text-gray-600 hover:bg-black/5 hover:text-black',
                                            'relative inline-flex items-center px-6 py-4 text-sm rounded-full transition-all duration-300'
                                        ]"
                                    />
                                    <span v-else v-html="link.label" class="relative inline-flex items-center px-6 py-4 text-sm font-medium text-gray-400 cursor-not-allowed"></span>
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
