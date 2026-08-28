<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { PhPlus, PhPencilSimple, PhTrash, PhMagnifyingGlass, PhCaretDown, PhCaretRight, PhPackage } from '@phosphor-icons/vue';
import { ref, watch } from 'vue';

const props = defineProps({
    locations: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');
const expandedRows = ref(new Set());
const locationItems = ref({});
const loadingItems = ref(new Set());

// Debounce search
let timeout = null;
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('locations.index'), { search: value }, { preserveState: true, replace: true });
    }, 300);
});

const deleteLocation = (id) => {
    if (confirm('Yakin ingin menghapus lokasi/rak ini?')) {
        router.delete(route('locations.destroy', id));
    }
};

const toggleRow = async (loc) => {
    if (expandedRows.value.has(loc.id)) {
        expandedRows.value.delete(loc.id);
    } else {
        expandedRows.value.add(loc.id);
        
        // Fetch items if not loaded yet
        if (!locationItems.value[loc.id]) {
            loadingItems.value.add(loc.id);
            try {
                const response = await fetch(route('locations.items', loc.id));
                const data = await response.json();
                locationItems.value[loc.id] = data.items;
            } catch (error) {
                console.error('Failed to load items:', error);
                locationItems.value[loc.id] = [];
            } finally {
                loadingItems.value.delete(loc.id);
            }
        }
    }
};
</script>

<template>
    <Head title="Manajemen Lokasi Gudang" />

    <AuthenticatedLayout>
        <template #header>
            Manajemen Lokasi Rak
        </template>

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-6">
            <div class="relative w-full md:w-96">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <PhMagnifyingGlass class="h-5 w-5 text-gray-600" />
                </div>
                <input 
                    v-model="search"
                    type="text" 
                    class="block w-full pl-12 pr-4 py-3 border border-black/10 rounded-full bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black sm:text-sm transition-all duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] shadow-[0_4px_20px_rgb(0,0,0,0.03)]" 
                    placeholder="Cari Kode atau Zona..." 
                />
            </div>
            
            <Link :href="route('locations.create')" class="group inline-flex items-center justify-between pl-6 pr-2 py-2 border border-transparent text-sm font-semibold rounded-full text-white bg-terracotta-600 hover:bg-terracotta-700 transition-all duration-700 ease-[cubic-bezier(0.32,0.72,0,1)] hover:scale-[0.98] w-full md:w-auto gap-6 shadow-[0_8px_30px_rgb(193,83,53,0.3)]">
                <span>Tambah Rak</span>
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
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs w-8"></th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Kode Rak</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Nama Rak</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Zona</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Tipe Penyimpanan</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Status</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Kapasitas</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            <template v-for="loc in locations.data" :key="loc.id">
                                <!-- Main Row -->
                                <tr class="hover:bg-black/[0.01] transition-colors duration-300 group cursor-pointer" @click="toggleRow(loc)">
                                    <td class="px-6 py-5">
                                        <component :is="expandedRows.has(loc.id) ? PhCaretDown : PhCaretRight" class="w-4 h-4 text-gray-400 transition-transform duration-200" />
                                    </td>
                                    <td class="px-6 py-5 font-mono text-gray-600 font-bold text-xs">{{ loc.code }}</td>
                                    <td class="px-6 py-5 font-semibold text-black tracking-tight">{{ loc.name }}</td>
                                    <td class="px-6 py-5 text-gray-600">{{ loc.zone_name || '-' }}</td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center rounded-full bg-black/5 px-3 py-1 text-xs font-semibold text-black">
                                            {{ loc.storage_type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span :class="[
                                            'inline-flex items-center rounded-full px-3 py-1 text-xs font-bold tracking-tight',
                                            loc.status === 'Active' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'
                                        ]">
                                            {{ loc.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 w-48">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                                <div :class="[
                                                        'h-full rounded-full transition-all duration-500',
                                                        loc.capacity_percentage > 90 ? 'bg-red-500' : (loc.capacity_percentage > 70 ? 'bg-amber-400' : 'bg-emerald-400')
                                                    ]"
                                                    :style="{ width: loc.capacity_percentage + '%' }"
                                                ></div>
                                            </div>
                                            <span class="text-xs font-semibold text-gray-600 w-8 text-right">{{ loc.capacity_percentage }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300" @click.stop>
                                            <Link :href="route('locations.edit', loc.id)" class="p-2 text-gray-600 hover:text-black hover:bg-black/5 rounded-full transition-colors duration-300" aria-label="Edit">
                                                <PhPencilSimple class="w-4 h-4" />
                                            </Link>
                                            <button @click="deleteLocation(loc.id)" class="p-2 text-gray-600 hover:text-red-700 hover:bg-red-50 rounded-full transition-colors duration-300" aria-label="Hapus">
                                                <PhTrash class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Expanded Row - Items in this location -->
                                <tr v-if="expandedRows.has(loc.id)">
                                    <td colspan="8" class="px-6 py-4 bg-gray-50">
                                        <div class="ml-8">
                                            <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-3">Isi Rak</h4>
                                            
                                            <!-- Loading State -->
                                            <div v-if="loadingItems.has(loc.id)" class="text-center py-4">
                                                <p class="text-xs text-gray-500">Memuat data barang...</p>
                                            </div>

                                            <!-- Empty State -->
                                            <div v-else-if="!locationItems[loc.id] || locationItems[loc.id].length === 0" class="text-center py-4">
                                                <PhPackage class="w-8 h-8 text-gray-300 mx-auto mb-2" />
                                                <p class="text-xs text-gray-500">Belum ada barang di rak ini</p>
                                            </div>

                                            <!-- Items List -->
                                            <div v-else class="space-y-2">
                                                <div v-for="item in locationItems[loc.id]" :key="item.id" class="flex items-center justify-between bg-white rounded-xl px-4 py-3 border border-black/5">
                                                    <div class="flex items-center gap-4">
                                                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden border border-black/5">
                                                            <img v-if="item.photo" :src="'/storage/' + item.photo" alt="Photo" class="w-full h-full object-cover" />
                                                            <PhPackage v-else class="w-5 h-5 text-gray-400" />
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-semibold text-black">{{ item.name }}</p>
                                                            <p class="text-xs text-gray-500 font-mono">{{ item.sku }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-sm font-bold text-black">{{ item.pivot.quantity }} {{ item.unit || 'Pcs' }}</p>
                                                        <p class="text-xs text-gray-500">{{ item.category }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <tr v-if="locations.data.length === 0">
                                <td colspan="8" class="px-8 py-24 text-center text-gray-600">
                                    <div role="status" class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                        <div class="w-16 h-16 rounded-full bg-black/5 flex items-center justify-center mb-4">
                                            <PhPlus class="w-8 h-8 text-gray-600" />
                                        </div>
                                        <h3 class="text-lg font-semibold text-black mb-1">Belum ada lokasi</h3>
                                        <p class="text-sm text-gray-600 mb-6">Tambahkan rak penyimpanan untuk memetakan gudang Anda.</p>
                                        <Link :href="route('locations.create')" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-semibold rounded-full text-white bg-black hover:bg-gray-800 transition-colors duration-300">
                                            Tambah Rak
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div v-if="locations.links && locations.links.length > 3" class="px-8 py-5 border-t border-black/5 flex items-center justify-between bg-black/[0.01]">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link v-if="locations.prev_page_url" :href="locations.prev_page_url" class="relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-black bg-white hover:bg-black/5 transition-colors">Previous</Link>
                        <span v-else class="relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-gray-600 bg-gray-50 cursor-not-allowed">Previous</span>
                        
                        <Link v-if="locations.next_page_url" :href="locations.next_page_url" class="ml-3 relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-black bg-white hover:bg-black/5 transition-colors">Next</Link>
                        <span v-else class="ml-3 relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-gray-600 bg-gray-50 cursor-not-allowed">Next</span>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-600">
                                Menampilkan <span class="font-medium text-black">{{ locations.from }}</span> - <span class="font-medium text-black">{{ locations.to }}</span> dari <span class="font-medium text-black">{{ locations.total }}</span>
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-full shadow-sm bg-white border border-black/10 p-1 gap-1" aria-label="Pagination">
                                <template v-for="(link, i) in locations.links" :key="i">
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
