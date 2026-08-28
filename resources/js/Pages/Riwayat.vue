<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { PhArrowDownLeft, PhArrowUpRight, PhMagnifyingGlass, PhClock, PhCheckCircle, PhWarning } from '@phosphor-icons/vue';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    inbounds: Object,
    outbounds: Object,
    transactions: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const type = ref(props.filters?.type || '');

let timeout = null;
watch([search, type], ([searchVal, typeVal]) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('riwayat.index'), {
            search: searchVal || undefined,
            type: typeVal || undefined,
        }, { preserveState: true, replace: true });
    }, 300);
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const formatMonth = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        month: 'long',
        year: 'numeric',
    });
};

const stats = computed(() => {
    const inboundData = props.inbounds?.data || [];
    const outboundData = props.outbounds?.data || [];
    return {
        totalInbound: inboundData.reduce((sum, t) => sum + t.quantity, 0),
        totalOutbound: outboundData.reduce((sum, t) => sum + t.quantity, 0),
        pendingCount: [...inboundData, ...outboundData].filter(t => t.status === 'pending').length,
        completedCount: [...inboundData, ...outboundData].filter(t => t.status === 'completed').length,
    };
});

const groupedTransactions = computed(() => {
    if (type.value !== '' || !props.transactions) return null;
    
    const sorted = [...props.transactions].sort((a, b) => 
        new Date(b.transaction_date) - new Date(a.transaction_date)
    );
    
    const groups = {};
    sorted.forEach(tx => {
        const month = formatMonth(tx.transaction_date);
        if (!groups[month]) groups[month] = [];
        groups[month].push(tx);
    });
    return groups;
});
</script>

<template>
    <Head title="Riwayat Transaksi" />

    <AuthenticatedLayout>
        <template #header>
            Riwayat Transaksi
        </template>

        <!-- Summary Metrics -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white border border-black/10 rounded-[1.25rem] p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center">
                        <PhArrowDownLeft class="w-4 h-4 text-emerald-600" />
                    </div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Masuk</span>
                </div>
                <p class="text-3xl font-bold tracking-tighter text-black">{{ stats.totalInbound }}</p>
                <p class="text-[10px] text-gray-400 font-medium mt-1">unit diterima</p>
            </div>
            <div class="bg-white border border-black/10 rounded-[1.25rem] p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center">
                        <PhArrowUpRight class="w-4 h-4 text-red-600" />
                    </div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Keluar</span>
                </div>
                <p class="text-3xl font-bold tracking-tighter text-black">{{ stats.totalOutbound }}</p>
                <p class="text-[10px] text-gray-400 font-medium mt-1">unit dikirim</p>
            </div>
            <div class="bg-white border border-black/10 rounded-[1.25rem] p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full bg-amber-50 flex items-center justify-center">
                        <PhClock class="w-4 h-4 text-amber-600" />
                    </div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pending</span>
                </div>
                <p class="text-3xl font-bold tracking-tighter text-black">{{ stats.pendingCount }}</p>
                <p class="text-[10px] text-gray-400 font-medium mt-1">menunggu proses</p>
            </div>
            <div class="bg-white border border-black/10 rounded-[1.25rem] p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full bg-black/5 flex items-center justify-center">
                        <PhCheckCircle class="w-4 h-4 text-black" />
                    </div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Selesai</span>
                </div>
                <p class="text-3xl font-bold tracking-tighter text-black">{{ stats.completedCount }}</p>
                <p class="text-[10px] text-gray-400 font-medium mt-1">transaksi</p>
            </div>
        </div>

        <!-- Tabs & Search -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-6">
            <div class="flex items-center gap-2">
                <button
                    @click="type = ''"
                    :class="[
                        type === ''
                            ? 'bg-black text-white'
                            : 'bg-white text-gray-600 hover:bg-gray-50',
                        'inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-semibold transition-colors border border-black/5'
                    ]"
                >
                    Semua
                </button>
                <button
                    @click="type = 'inbound'"
                    :class="[
                        type === 'inbound'
                            ? 'bg-black text-white'
                            : 'bg-white text-gray-600 hover:bg-gray-50',
                        'inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-semibold transition-colors border border-black/5'
                    ]"
                >
                    <PhArrowDownLeft class="w-4 h-4" />
                    Masuk
                </button>
                <button
                    @click="type = 'outbound'"
                    :class="[
                        type === 'outbound'
                            ? 'bg-black text-white'
                            : 'bg-white text-gray-600 hover:bg-gray-50',
                        'inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-semibold transition-colors border border-black/5'
                    ]"
                >
                    <PhArrowUpRight class="w-4 h-4" />
                    Keluar
                </button>
            </div>

            <div class="relative w-full md:w-80">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <PhMagnifyingGlass class="h-5 w-5 text-gray-600" />
                </div>
                <input
                    v-model="search"
                    type="text"
                    class="block w-full pl-12 pr-4 py-3 border border-black/10 rounded-full bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black sm:text-sm transition-all duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] shadow-[0_4px_20px_rgb(0,0,0,0.03)]"
                    placeholder="Cari SKU atau Nama Barang..."
                />
            </div>
        </div>

        <!-- Unified Timeline View (when "Semua" is selected) -->
        <div v-if="type === '' && groupedTransactions">
            <template v-for="(items, month) in groupedTransactions" :key="month">
                <!-- Month Header -->
                <div class="flex items-center gap-4 mb-4 mt-2">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ month }}</h3>
                    <div class="flex-1 h-px bg-black/5"></div>
                </div>

                <!-- Timeline Items -->
                <div class="relative pl-8 mb-8 before:content-[''] before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-px before:bg-black/10">
                    <div
                        v-for="tx in items"
                        :key="`${tx.type}-${tx.id}`"
                        class="relative mb-4 last:mb-0"
                    >
                        <!-- Timeline Dot -->
                        <div :class="[
                            'absolute -left-8 top-5 w-6 h-6 rounded-full flex items-center justify-center ring-4 ring-white',
                            tx.type === 'inbound' ? 'bg-emerald-500' : 'bg-red-500'
                        ]">
                            <component :is="tx.type === 'inbound' ? PhArrowDownLeft : PhArrowUpRight" class="w-3 h-3 text-white" weight="bold" />
                        </div>

                        <!-- Transaction Card -->
                        <div class="bg-white border border-black/10 rounded-2xl p-5 hover:border-black/20 transition-colors">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 mb-1">
                                        <span :class="[
                                            'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                                            tx.type === 'inbound' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'
                                        ]">
                                            {{ tx.type === 'inbound' ? 'Masuk' : 'Keluar' }}
                                        </span>
                                        <span class="text-xs text-gray-400">{{ formatDate(tx.transaction_date) }}</span>
                                    </div>
                                    <h4 class="font-semibold text-black tracking-tight">{{ tx.item?.name || 'Barang tidak ditemukan' }}</h4>
                                    <p class="text-xs text-gray-500 font-mono mt-0.5">{{ tx.item?.sku }}</p>
                                </div>
                                <div class="text-right">
                                    <p :class="[
                                        'text-2xl font-bold tracking-tighter',
                                        tx.type === 'inbound' ? 'text-emerald-600' : 'text-red-600'
                                    ]">
                                        {{ tx.type === 'inbound' ? '+' : '-' }}{{ tx.quantity }}
                                    </p>
                                    <span :class="[
                                        'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold mt-1',
                                        tx.status === 'completed' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600'
                                    ]">
                                        {{ tx.status === 'completed' ? 'Selesai' : 'Pending' }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-black/5 flex items-center gap-4 text-xs text-gray-500">
                                <span v-if="tx.supplier">Supplier: <strong class="text-black">{{ tx.supplier }}</strong></span>
                                <span v-if="tx.recipient">Penerima: <strong class="text-black">{{ tx.recipient }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <div v-if="Object.keys(groupedTransactions).length === 0" class="bg-white border border-black/10 rounded-[1.5rem] p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-black/5 flex items-center justify-center mx-auto mb-4">
                    <PhClock class="w-8 h-8 text-gray-400" />
                </div>
                <h3 class="text-lg font-semibold text-black mb-1">Belum ada riwayat</h3>
                <p class="text-sm text-gray-500">Riwayat transaksi akan muncul di sini.</p>
            </div>
        </div>

        <!-- Filtered Table View (when specific type is selected) -->
        <div v-else>
            <!-- Inbound Table -->
            <div v-if="type === 'inbound'" class="bg-white border border-black/10 rounded-[1.5rem] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-black/5">
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Tanggal</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">SKU</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Nama Barang</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Supplier</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Status</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            <tr v-for="tx in inbounds.data" :key="'inbound-' + tx.id" class="hover:bg-black/[0.01] transition-colors duration-300 group">
                                <td class="px-6 py-5 text-gray-600 font-medium">{{ formatDate(tx.transaction_date) }}</td>
                                <td class="px-6 py-5 font-mono text-gray-600 text-xs">{{ tx.item?.sku }}</td>
                                <td class="px-6 py-5 font-semibold text-black tracking-tight">{{ tx.item?.name }}</td>
                                <td class="px-6 py-5 text-gray-600">{{ tx.supplier || '-' }}</td>
                                <td class="px-6 py-5">
                                    <span v-if="tx.status === 'completed'" class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold tracking-tight bg-emerald-50 text-emerald-600">
                                        Completed
                                    </span>
                                    <span v-else class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold tracking-tight bg-amber-50 text-amber-600">
                                        Pending
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold tracking-tight text-emerald-600">
                                        +{{ tx.quantity }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="inbounds.data.length === 0">
                                <td colspan="6" class="px-8 py-24 text-center text-gray-600">
                                    <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                        <div class="w-16 h-16 rounded-full bg-black/5 flex items-center justify-center mb-4">
                                            <PhArrowDownLeft class="w-8 h-8 text-gray-400" />
                                        </div>
                                        <h3 class="text-lg font-semibold text-black mb-1">Belum ada transaksi masuk</h3>
                                        <p class="text-sm text-gray-500">Data akan muncul setelah ada penerimaan barang.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="inbounds.links && inbounds.links.length > 3" class="px-8 py-5 border-t border-black/5 flex items-center justify-between bg-black/[0.01]">
                    <p class="text-sm text-gray-600">
                        Menampilkan <span class="font-medium text-black">{{ inbounds.from }}</span> - <span class="font-medium text-black">{{ inbounds.to }}</span> dari <span class="font-medium text-black">{{ inbounds.total }}</span>
                    </p>
                    <nav class="inline-flex rounded-full shadow-sm bg-white border border-black/10 p-1 gap-1">
                        <template v-for="(link, i) in inbounds.links" :key="i">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                :class="[
                                    link.active ? 'bg-terracotta-600 text-white font-semibold' : 'text-gray-600 hover:bg-black/5 hover:text-black',
                                    'relative inline-flex items-center px-5 py-2.5 text-sm rounded-full transition-all duration-300'
                                ]"
                            >{{ link.label }}</Link>
                            <span v-else class="relative inline-flex items-center px-5 py-2.5 text-sm font-medium text-gray-400 cursor-not-allowed">{{ link.label }}</span>
                        </template>
                    </nav>
                </div>
            </div>

            <!-- Outbound Table -->
            <div v-if="type === 'outbound'" class="bg-white border border-black/10 rounded-[1.5rem] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-black/5">
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Tanggal</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">SKU</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Nama Barang</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Penerima</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Status</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            <tr v-for="tx in outbounds.data" :key="'outbound-' + tx.id" class="hover:bg-black/[0.01] transition-colors duration-300 group">
                                <td class="px-6 py-5 text-gray-600 font-medium">{{ formatDate(tx.transaction_date) }}</td>
                                <td class="px-6 py-5 font-mono text-gray-600 text-xs">{{ tx.item?.sku }}</td>
                                <td class="px-6 py-5 font-semibold text-black tracking-tight">{{ tx.item?.name }}</td>
                                <td class="px-6 py-5 text-gray-600">{{ tx.recipient || '-' }}</td>
                                <td class="px-6 py-5">
                                    <span v-if="tx.status === 'completed'" class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold tracking-tight bg-emerald-50 text-emerald-600">
                                        Completed
                                    </span>
                                    <span v-else class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold tracking-tight bg-amber-50 text-amber-600">
                                        Pending
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-bold tracking-tight text-red-600">
                                        -{{ tx.quantity }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="outbounds.data.length === 0">
                                <td colspan="6" class="px-8 py-24 text-center text-gray-600">
                                    <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                        <div class="w-16 h-16 rounded-full bg-black/5 flex items-center justify-center mb-4">
                                            <PhArrowUpRight class="w-8 h-8 text-gray-400" />
                                        </div>
                                        <h3 class="text-lg font-semibold text-black mb-1">Belum ada transaksi keluar</h3>
                                        <p class="text-sm text-gray-500">Data akan muncul setelah ada pengeluaran barang.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="outbounds.links && outbounds.links.length > 3" class="px-8 py-5 border-t border-black/5 flex items-center justify-between bg-black/[0.01]">
                    <p class="text-sm text-gray-600">
                        Menampilkan <span class="font-medium text-black">{{ outbounds.from }}</span> - <span class="font-medium text-black">{{ outbounds.to }}</span> dari <span class="font-medium text-black">{{ outbounds.total }}</span>
                    </p>
                    <nav class="inline-flex rounded-full shadow-sm bg-white border border-black/10 p-1 gap-1">
                        <template v-for="(link, i) in outbounds.links" :key="i">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                :class="[
                                    link.active ? 'bg-terracotta-600 text-white font-semibold' : 'text-gray-600 hover:bg-black/5 hover:text-black',
                                    'relative inline-flex items-center px-5 py-2.5 text-sm rounded-full transition-all duration-300'
                                ]"
                            >{{ link.label }}</Link>
                            <span v-else class="relative inline-flex items-center px-5 py-2.5 text-sm font-medium text-gray-400 cursor-not-allowed">{{ link.label }}</span>
                        </template>
                    </nav>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
