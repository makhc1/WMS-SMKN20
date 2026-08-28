<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { PhPlus, PhPencilSimple, PhTrash, PhMagnifyingGlass, PhQrCode, PhImageSquare } from '@phosphor-icons/vue';
import { ref, watch, computed } from 'vue';

const page = usePage();
const canDelete = computed(() => ['Admin', 'Warehouse Manager'].includes(page.props.auth.user.role));

const props = defineProps({
    items: Object,
    filters: Object,
    categories: Array
});

const search = ref(props.filters?.search || '');
const category = ref(props.filters?.category || '');

// Debounce search
let timeout = null;
watch([search, category], ([searchVal, categoryVal]) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('items.index'), { search: searchVal, category: categoryVal }, { preserveState: true, replace: true });
    }, 300);
});

const deleteItem = (id, name) => {
    if (confirm(`Yakin ingin menghapus "${name}"? Data yang terhapus tidak dapat dikembalikan.`)) {
        router.delete(route('items.destroy', id), {
            preserveScroll: true,
            onError: (errors) => {
                if (errors.error) {
                    alert(errors.error);
                }
            }
        });
    }
};

const getStockStatus = (item) => {
    if (item.quantity === 0) return { label: 'Out of Stock', class: 'bg-red-50 text-red-600' };
    if (item.quantity <= item.low_stock_threshold) return { label: 'Low Stock', class: 'bg-amber-50 text-amber-600' };
    return { label: 'In Stock', class: 'bg-emerald-50 text-emerald-600' };
};
</script>

<template>
    <Head title="Master Barang" />

    <AuthenticatedLayout>
        <template #header>
            Master Barang
        </template>

        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700 font-medium">
            {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash?.message" class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700 font-medium">
            {{ $page.props.flash.message }}
        </div>
        <div v-if="$page.props.errors?.error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 font-medium">
            {{ $page.props.errors.error }}
        </div>

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-6">
            <div class="flex flex-col md:flex-row gap-6 w-full md:w-auto">
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
                <div class="relative w-full md:w-48">
                    <select v-model="category" class="block w-full pl-4 pr-10 py-3 border border-black/10 rounded-full bg-white text-gray-600 focus:outline-none focus:ring-2 focus:ring-black focus:border-black sm:text-sm transition-all duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] shadow-[0_4px_20px_rgb(0,0,0,0.03)] appearance-none">
                        <option value="">Semua Kategori</option>
                        <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                    </select>
                </div>
            </div>
            
            <Link :href="route('items.create')" class="group inline-flex items-center justify-between pl-6 pr-2 py-2 border border-transparent text-sm font-semibold rounded-full text-white bg-terracotta-600 hover:bg-terracotta-700 transition-all duration-700 ease-[cubic-bezier(0.32,0.72,0,1)] hover:scale-[0.98] w-full md:w-auto gap-6 shadow-[0_8px_30px_rgb(193,83,53,0.3)]">
                <span>Tambah Barang</span>
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
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Foto</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">SKU</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Nama Barang</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Kategori</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs text-right">Harga Dasar</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs text-right">Status & Stok</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            <tr v-for="item in items.data" :key="item.id" class="hover:bg-black/[0.01] transition-colors duration-300 group">
                                <td class="px-6 py-6">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden border border-black/5">
                                        <img v-if="item.photo" :src="'/storage/' + item.photo" alt="Photo" class="w-full h-full object-cover" />
                                        <PhImageSquare v-else class="w-5 h-5 text-gray-600" />
                                    </div>
                                </td>
                                <td class="px-6 py-5 font-mono text-gray-600 text-xs">{{ item.sku }}</td>
                                <td class="px-6 py-5 font-semibold text-black tracking-tight max-w-[12rem] truncate">{{ item.name }}</td>
                                <td class="px-6 py-5 text-gray-600">{{ item.category }}</td>
                                <td class="px-6 py-5 text-right font-mono text-gray-600">
                                    Rp {{ item.base_price ? parseFloat(item.base_price).toLocaleString('id-ID') : '0' }}
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex flex-col items-end gap-1.5">
                                        <span :class="['inline-flex px-4 sm:px-6 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider', getStockStatus(item).class]">
                                            {{ getStockStatus(item).label }}
                                        </span>
                                        <span class="text-sm font-semibold text-black">{{ item.quantity }} {{ item.unit || 'Pcs' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <Link :href="route('items.show', item.id)" class="p-2 text-gray-600 hover:text-black hover:bg-black/5 rounded-full transition-colors duration-300" title="Detail & QR">
                                            <PhQrCode class="w-4 h-4" />
                                        </Link>
                                        <Link :href="route('items.edit', item.id)" class="p-2 text-gray-600 hover:text-black hover:bg-black/5 rounded-full transition-colors duration-300" title="Edit">
                                            <PhPencilSimple class="w-4 h-4" />
                                        </Link>
                                        <button v-if="canDelete" @click="deleteItem(item.id, item.name)" class="p-2 text-gray-600 hover:text-red-700 hover:bg-red-50 rounded-full transition-colors duration-300" title="Hapus">
                                            <PhTrash class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="items.data.length === 0">
                                <td colspan="7" class="px-8 py-24 text-center text-gray-600">
                                    <div role="status" class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                        <div class="w-16 h-16 rounded-full bg-black/5 flex items-center justify-center mb-4">
                                            <PhPlus class="w-8 h-8 text-gray-600" />
                                        </div>
                                        <h3 class="text-lg font-semibold text-black mb-1">Belum ada data barang</h3>
                                        <p class="text-sm text-gray-600 mb-6">Tambahkan master barang pertama Anda untuk mulai mengelola stok dan melacak pergerakan gudang.</p>
                                        <Link :href="route('items.create')" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-semibold rounded-full text-white bg-black hover:bg-gray-800 transition-colors duration-300">
                                            Tambah Barang Pertama
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div v-if="items.links && items.links.length > 3" class="px-8 py-5 border-t border-black/5 flex items-center justify-between bg-black/[0.01]">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link v-if="items.prev_page_url" :href="items.prev_page_url" class="relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-black bg-white hover:bg-black/5 transition-colors">Previous</Link>
                        <span v-else class="relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-gray-600 bg-gray-50 cursor-not-allowed">Previous</span>
                        
                        <Link v-if="items.next_page_url" :href="items.next_page_url" class="ml-3 relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-black bg-white hover:bg-black/5 transition-colors">Next</Link>
                        <span v-else class="ml-3 relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-gray-600 bg-gray-50 cursor-not-allowed">Next</span>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-600">
                                Menampilkan <span class="font-medium text-black">{{ items.from }}</span> - <span class="font-medium text-black">{{ items.to }}</span> dari <span class="font-medium text-black">{{ items.total }}</span>
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-full shadow-sm bg-white border border-black/10 p-1 gap-1" aria-label="Pagination">
                                <template v-for="(link, i) in items.links" :key="i">
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
