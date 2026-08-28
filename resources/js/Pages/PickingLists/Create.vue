<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import { PhArrowLeft, PhPlus, PhTrash, PhPackage, PhListChecks, PhMapPin } from '@phosphor-icons/vue';

const props = defineProps({
    items: Array,
    locations: Array,
});

const form = useForm({
    notes: '',
    items: [],
});

const selectedItem = ref('');
const selectedLocation = ref('');
const selectedQuantity = ref(1);

const addItem = () => {
    if (!selectedItem.value || !selectedLocation.value || selectedQuantity.value < 1) {
        return;
    }

    const item = props.items.find(i => i.id === selectedItem.value);
    const location = props.locations.find(l => l.id === selectedLocation.value);

    if (item && location) {
        form.items.push({
            item_id: item.id,
            location_id: location.id,
            item_name: item.name,
            item_sku: item.sku,
            location_code: location.code,
            location_name: location.name,
            quantity: selectedQuantity.value,
        });

        selectedItem.value = '';
        selectedLocation.value = '';
        selectedQuantity.value = 1;
    }
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const submit = () => {
    form.post(route('picking-lists.store'));
};
</script>

<template>
    <Head title="Buat Picking List Baru" />

    <AuthenticatedLayout>
        <template #header>
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="route('picking-lists.index')" class="w-11 h-11 rounded-full bg-white hover:bg-black/5 flex items-center justify-center border border-black/10 text-gray-700 hover:text-black transition-all shadow-sm hover:scale-105 active:scale-95">
                        <PhArrowLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-black leading-tight">
                            Buat Picking List Baru
                        </h2>
                        <p class="text-xs text-gray-500 font-medium">Susun daftar pengambilan barang di rak untuk staf gudang.</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="max-w-4xl mx-auto pb-12">
            <div class="bg-white border border-black/10 rounded-3xl shadow-sm overflow-hidden">
                <form @submit.prevent="submit" class="p-8 sm:p-10 space-y-10 divide-y divide-black/5">
                    
                    <!-- Section 1: Tambah Item ke Daftar -->
                    <div class="space-y-6 pt-0">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                <PhListChecks class="w-5 h-5" weight="duotone" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold tracking-tight text-black">Pilih Barang &amp; Lokasi Rak</h3>
                                <p class="text-xs text-gray-500">Pilih barang dan rak pengambilan, lalu klik tombol tambah item.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Select Item -->
                            <div class="md:col-span-1">
                                <label class="block text-xs font-semibold text-gray-700 mb-2">
                                    Pilih Barang
                                </label>
                                <select
                                    v-model="selectedItem"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                >
                                    <option value="">-- Pilih Barang --</option>
                                    <option v-for="item in items" :key="item.id" :value="item.id">
                                        [{{ item.sku }}] {{ item.name }} (Stok: {{ item.quantity }})
                                    </option>
                                </select>
                            </div>

                            <!-- Select Location -->
                            <div class="md:col-span-1">
                                <label class="block text-xs font-semibold text-gray-700 mb-2">
                                    Lokasi Rak Pengambilan
                                </label>
                                <select
                                    v-model="selectedLocation"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                >
                                    <option value="">-- Pilih Rak --</option>
                                    <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                                        [{{ loc.code }}] {{ loc.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Quantity & Add Button -->
                            <div class="md:col-span-1 flex items-end gap-2">
                                <div class="flex-1">
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">
                                        Kuantitas
                                    </label>
                                    <input
                                        type="number"
                                        min="1"
                                        v-model="selectedQuantity"
                                        class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm font-semibold"
                                    />
                                </div>
                                <button
                                    type="button"
                                    @click="addItem"
                                    :disabled="!selectedItem || !selectedLocation"
                                    class="px-5 py-3 bg-black hover:bg-gray-800 text-white rounded-xl text-xs font-semibold shadow-sm transition-all hover:scale-105 active:scale-95 disabled:opacity-40 disabled:hover:scale-100 flex items-center gap-1.5 whitespace-nowrap"
                                >
                                    <PhPlus class="w-4 h-4" />
                                    <span>Tambah</span>
                                </button>
                            </div>
                        </div>

                        <!-- Selected Items List -->
                        <div v-if="form.items.length > 0" class="pt-4 space-y-3">
                            <p class="text-xs font-semibold text-gray-500">Item Ditambahkan ({{ form.items.length }})</p>

                            <div class="space-y-2">
                                <div
                                    v-for="(item, index) in form.items"
                                    :key="index"
                                    class="flex items-center justify-between p-3.5 rounded-xl bg-gray-50 border border-black/5 hover:border-black/10 transition-all"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-terracotta-50 flex items-center justify-center text-terracotta-600 flex-shrink-0">
                                            <PhPackage class="w-4 h-4" weight="duotone" />
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-black">{{ item.item_name }}</p>
                                            <p class="text-xs text-gray-400 font-mono flex items-center gap-2">
                                                <span>{{ item.item_sku }}</span>
                                                <span>•</span>
                                                <span class="text-purple-600 font-medium flex items-center gap-1">
                                                    <PhMapPin class="w-3 h-3" />
                                                    {{ item.location_code }} ({{ item.location_name }})
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="px-2.5 py-1 bg-white border border-black/10 rounded-full text-xs font-semibold text-black">
                                            {{ item.quantity }} Unit
                                        </span>
                                        <button
                                            type="button"
                                            @click="removeItem(index)"
                                            class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-full transition-all"
                                        >
                                            <PhTrash class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="pt-4 text-center py-4 text-gray-400">
                            <p class="text-xs font-medium">Belum ada item yang ditambahkan ke Picking List ini.</p>
                        </div>
                        <InputError class="mt-2" :message="form.errors.items" />
                    </div>

                    <!-- Section 2: Catatan Dokumen -->
                    <div class="space-y-6 pt-10">
                        <div>
                            <label for="notes" class="block text-xs font-semibold text-gray-700 mb-2">
                                Catatan Tambahan untuk Staf Pengambil
                            </label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="3"
                                class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm placeholder-gray-400"
                                placeholder="Tuliskan catatan prioritas pengambilan atau instruksi khusus..."
                            ></textarea>
                            <InputError class="mt-1.5" :message="form.errors.notes" />
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="flex items-center justify-between pt-8">
                        <Link
                            :href="route('picking-lists.index')"
                            class="px-6 py-3 text-sm font-semibold text-gray-600 hover:text-black transition-all rounded-full hover:bg-black/5"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing || form.items.length === 0"
                            class="inline-flex items-center justify-center px-8 py-3.5 bg-terracotta-600 hover:bg-terracotta-700 text-white font-semibold text-sm rounded-full shadow-[0_8px_25px_rgba(193,83,53,0.25)] hover:scale-[1.02] active:scale-95 transition-all duration-300 disabled:opacity-50"
                        >
                            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan &amp; Terbitkan Picking List' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
