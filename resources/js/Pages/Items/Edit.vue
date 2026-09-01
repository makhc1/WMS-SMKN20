<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import { PhArrowLeft, PhImageSquare, PhCube, PhCurrencyDollar, PhTrash } from '@phosphor-icons/vue';
import { ref, computed } from 'vue';

const props = defineProps({
    item: Object,
});

const page = usePage();
const canAdjustStock = computed(() => ['Admin', 'Warehouse Manager'].includes(page.props.auth.user.role));

const form = useForm({
    sku: props.item.sku,
    name: props.item.name,
    description: props.item.description || '',
    category: props.item.category,
    unit: props.item.unit || 'Pcs',
    base_price: props.item.base_price || '',
    low_stock_threshold: props.item.low_stock_threshold,
    quantity: props.item.quantity,
    photo: null,
    _method: 'put',
});

const photoPreview = ref(props.item.photo ? '/storage/' + props.item.photo : null);
const fileInput = ref(null);

const handlePhotoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.photo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const triggerFileInput = () => {
    fileInput.value.click();
};

const removePhoto = () => {
    form.photo = null;
    photoPreview.value = null;
    if (fileInput.value) fileInput.value.value = '';
};

const submit = () => {
    form.post(route('items.update', props.item.id));
};
</script>

<template>
    <Head :title="`Edit Barang - ${item.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="route('items.index')" class="w-11 h-11 rounded-full bg-white hover:bg-black/5 flex items-center justify-center border border-black/10 text-gray-700 hover:text-black transition-all shadow-sm hover:scale-105 active:scale-95">
                        <PhArrowLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-black leading-tight">
                            Edit Data Barang
                        </h2>
                        <p class="text-xs text-gray-500 font-medium">Perbarui informasi katalog barang: {{ item.name }} ({{ item.sku }})</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="max-w-4xl mx-auto pb-12">
            <div class="bg-white border border-black/10 rounded-3xl shadow-sm overflow-hidden">
                <form @submit.prevent="submit" class="p-8 sm:p-10 space-y-10 divide-y divide-black/5">
                    
                    <!-- Section 1: Foto Produk -->
                    <div class="space-y-6 pt-0">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-terracotta-50 flex items-center justify-center text-terracotta-600">
                                <PhImageSquare class="w-5 h-5" weight="duotone" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold tracking-tight text-black">Foto Produk</h3>
                                <p class="text-xs text-gray-500">Perbarui gambar barang untuk katalog sistem.</p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            <div 
                                class="w-32 h-32 rounded-2xl border border-black/10 flex flex-col items-center justify-center bg-gray-50 overflow-hidden group cursor-pointer hover:border-black/30 transition-all relative"
                                @click="triggerFileInput"
                            >
                                <img v-if="photoPreview" :src="photoPreview" alt="Preview" class="w-full h-full object-cover" />
                                <div v-else class="flex flex-col items-center justify-center text-gray-400 group-hover:text-black transition-colors p-4 text-center">
                                    <PhImageSquare class="w-7 h-7 mb-1" />
                                    <span class="text-xs font-medium">Pilih Foto</span>
                                </div>
                            </div>

                            <div class="flex-1 space-y-3 text-center sm:text-left">
                                <input type="file" ref="fileInput" class="hidden" accept="image/png, image/jpeg, image/webp" @change="handlePhotoChange" />
                                <div class="flex flex-wrap items-center gap-3 justify-center sm:justify-start">
                                    <button 
                                        type="button" 
                                        @click="triggerFileInput" 
                                        class="px-5 py-2.5 bg-white hover:bg-gray-50 text-black border border-black/10 rounded-full text-xs font-semibold shadow-sm transition-all hover:scale-105 active:scale-95"
                                    >
                                        {{ photoPreview ? 'Ganti Foto' : 'Unggah Foto' }}
                                    </button>
                                    <button 
                                        v-if="photoPreview"
                                        type="button" 
                                        @click="removePhoto" 
                                        class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-full text-xs font-semibold transition-all active:scale-95 flex items-center gap-1.5"
                                    >
                                        <PhTrash class="w-3.5 h-3.5" />
                                        <span>Hapus</span>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400">Format: JPG, PNG, WEBP (Maksimal 2MB).</p>
                                <InputError :message="form.errors.photo" />
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Informasi Dasar -->
                    <div class="space-y-6 pt-10">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                <PhCube class="w-5 h-5" weight="duotone" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold tracking-tight text-black">Informasi Produk</h3>
                                <p class="text-xs text-gray-500">Kode identitas, nama, dan kategori barang.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- SKU -->
                            <div>
                                <label for="sku" class="block text-xs font-semibold text-gray-700 mb-2">
                                    SKU / Kode Barang <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="sku"
                                    type="text"
                                    v-model="form.sku"
                                    required
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm font-mono uppercase"
                                />
                                <InputError class="mt-1.5" :message="form.errors.sku" />
                            </div>

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Nama Barang <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="name"
                                    type="text"
                                    v-model="form.name"
                                    required
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.name" />
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="category"
                                    type="text"
                                    v-model="form.category"
                                    required
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.category" />
                            </div>

                            <!-- Unit -->
                            <div>
                                <label for="unit" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Satuan Unit <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="unit"
                                    v-model="form.unit"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                    required
                                >
                                    <option value="Pcs">Pcs (Satuan)</option>
                                    <option value="Box">Box (Kotak)</option>
                                    <option value="Kg">Kg (Kilogram)</option>
                                    <option value="Lusin">Lusin (12 Pcs)</option>
                                    <option value="Rim">Rim (500 Lembar)</option>
                                    <option value="Set">Set</option>
                                    <option value="Unit">Unit</option>
                                </select>
                                <InputError class="mt-1.5" :message="form.errors.unit" />
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Harga & Batas Stok -->
                    <div class="space-y-6 pt-10">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                                <PhCurrencyDollar class="w-5 h-5" weight="duotone" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold tracking-tight text-black">Harga &amp; Ambang Stok</h3>
                                <p class="text-xs text-gray-500">Pengaturan harga dasar, batas peringatan, dan penyesuaian stok.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Base Price -->
                            <div>
                                <label for="base_price" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Harga Dasar (Rp)
                                </label>
                                <input
                                    id="base_price"
                                    type="number"
                                    min="0"
                                    v-model="form.base_price"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.base_price" />
                            </div>

                            <!-- Low Stock Threshold -->
                            <div>
                                <label for="low_stock_threshold" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Batas Peringatan Stok Menipis <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="low_stock_threshold"
                                    type="number"
                                    min="0"
                                    v-model="form.low_stock_threshold"
                                    required
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.low_stock_threshold" />
                            </div>

                            <!-- Quantity Adjust (Admin/Manager only) -->
                            <div v-if="canAdjustStock" class="md:col-span-2 pt-2">
                                <label for="quantity" class="block text-xs font-semibold text-gray-700 mb-1">
                                    Penyesuaian Jumlah Stok Fisik (Manual Overwrite)
                                </label>
                                <p class="text-xs text-gray-400 mb-3">Hanya Admin dan Warehouse Manager yang dapat menyesuaikan total stok langsung. Stok tidak dapat diatur ke 0.</p>
                                <div class="flex items-center gap-3">
                                    <input
                                        id="quantity"
                                        type="number"
                                        min="1"
                                        v-model="form.quantity"
                                        class="w-44 px-4 py-2.5 rounded-xl bg-gray-50 border border-black/10 focus:border-black focus:ring-1 focus:ring-black font-semibold text-sm"
                                    />
                                    <span class="text-xs font-semibold text-gray-500">{{ form.unit }}</span>
                                </div>
                                <InputError class="mt-1.5" :message="form.errors.quantity" />
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Deskripsi Tambahan / Spesifikasi
                                </label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm placeholder-gray-400"
                                    placeholder="Tuliskan spesifikasi produk, merk, catatan penyimpanan, dll..."
                                ></textarea>
                                <InputError class="mt-1.5" :message="form.errors.description" />
                            </div>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="flex items-center justify-between pt-8">
                        <Link
                            :href="route('items.index')"
                            class="px-6 py-3 text-sm font-semibold text-gray-600 hover:text-black transition-all rounded-full hover:bg-black/5"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center px-8 py-3.5 bg-terracotta-600 hover:bg-terracotta-700 text-white font-semibold text-sm rounded-full shadow-[0_8px_25px_rgba(193,83,53,0.25)] hover:scale-[1.02] active:scale-95 transition-all duration-300 disabled:opacity-50"
                        >
                            <span>{{ form.processing ? 'Menyimpan...' : 'Perbarui Data Barang' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
