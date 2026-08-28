<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import { PhArrowLeft, PhCube, PhTruck, PhMapPin, PhSparkle, PhImageSquare, PhTrash } from '@phosphor-icons/vue';

const props = defineProps({
    locations: Array,
});

const form = useForm({
    sku: '',
    name: '',
    category: '',
    unit: 'Pcs',
    base_price: '',
    description: '',
    photo: null,
    transaction_date: new Date().toISOString().split('T')[0],
    quantity: 1,
    supplier: '',
    condition: 'Good',
    status: 'completed',
    notes: '',
    location_id: '',
    location_quantity: '',
});

const photoPreview = ref(null);
const fileInput = ref(null);

const generateSku = () => {
    const random = Math.random().toString(36).substring(2, 7).toUpperCase();
    form.sku = `ITM-${random}`;
};

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

const removePhoto = () => {
    form.photo = null;
    photoPreview.value = null;
    if (fileInput.value) fileInput.value.value = '';
};

const triggerFileInput = () => {
    fileInput.value.click();
};

const submit = () => {
    form.post(route('inbound.store'));
};
</script>

<template>
    <Head title="Input Barang Masuk (Inbound)" />

    <AuthenticatedLayout>
        <template #header>
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="route('inbound.index')" class="w-11 h-11 rounded-full bg-white hover:bg-black/5 flex items-center justify-center border border-black/10 text-gray-700 hover:text-black transition-all shadow-sm hover:scale-105 active:scale-95">
                        <PhArrowLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-black leading-tight">
                            Penerimaan Barang Masuk
                        </h2>
                        <p class="text-xs text-gray-500 font-medium">Catat transaksi barang masuk (Inbound) dari supplier ke gudang.</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="max-w-4xl mx-auto pb-12">
            <div class="bg-white border border-black/10 rounded-3xl shadow-sm overflow-hidden">
                <form @submit.prevent="submit" class="p-8 sm:p-10 space-y-10 divide-y divide-black/5">
                    
                    <!-- Section 1: Data Barang -->
                    <div class="space-y-6 pt-0">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                                <PhCube class="w-5 h-5" weight="duotone" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold tracking-tight text-black">Data Barang</h3>
                                <p class="text-xs text-gray-500">Jika SKU sudah terdaftar, stok barang akan otomatis ditambahkan.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- SKU -->
                            <div>
                                <label for="sku" class="block text-xs font-semibold text-gray-700 mb-2">
                                    SKU / Barcode Barang <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <input
                                        id="sku"
                                        type="text"
                                        v-model="form.sku"
                                        required
                                        placeholder="ITM-XXXX"
                                        class="flex-1 px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm font-mono uppercase"
                                    />
                                    <button 
                                        type="button" 
                                        @click="generateSku" 
                                        class="inline-flex items-center gap-1.5 px-4 py-3 bg-black hover:bg-gray-800 text-white text-xs font-semibold rounded-xl transition-all active:scale-95 shadow-sm"
                                    >
                                        <PhSparkle class="w-3.5 h-3.5" />
                                        <span>Auto</span>
                                    </button>
                                </div>
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
                                    placeholder="Nama barang diterima"
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
                                    placeholder="Elektronik, ATK, Perkakas, dll"
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

                            <!-- Base Price -->
                            <div>
                                <label for="base_price" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Harga Satuan Dasar (Rp)
                                </label>
                                <input
                                    id="base_price"
                                    type="number"
                                    min="0"
                                    v-model="form.base_price"
                                    placeholder="Contoh: 25000"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.base_price" />
                            </div>

                            <!-- Photo Upload -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">
                                    Foto Barang (Opsional)
                                </label>
                                <div class="flex items-center gap-3">
                                    <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="handlePhotoChange" />
                                    <button 
                                        type="button" 
                                        @click="triggerFileInput" 
                                        class="px-4 py-2.5 bg-white hover:bg-gray-50 text-black border border-black/10 rounded-xl text-xs font-semibold shadow-sm transition-all flex items-center gap-2"
                                    >
                                        <PhImageSquare class="w-4 h-4 text-terracotta-600" />
                                        <span>{{ photoPreview ? 'Ganti Foto' : 'Upload Foto' }}</span>
                                    </button>
                                    <button 
                                        v-if="photoPreview"
                                        type="button" 
                                        @click="removePhoto" 
                                        class="p-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition-all"
                                    >
                                        <PhTrash class="w-4 h-4" />
                                    </button>
                                    <span v-if="photoPreview" class="text-xs font-medium text-emerald-600">Foto dipilih</span>
                                </div>
                                <InputError class="mt-1.5" :message="form.errors.photo" />
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Data Penerimaan -->
                    <div class="space-y-6 pt-10">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                                <PhTruck class="w-5 h-5" weight="duotone" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold tracking-tight text-black">Detail Transaksi Kedatangan</h3>
                                <p class="text-xs text-gray-500">Rincian kuantitas, tanggal tiba, supplier, dan kondisi fisik.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Quantity -->
                            <div>
                                <label for="quantity" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Jumlah Diterima <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="quantity"
                                    type="number"
                                    min="1"
                                    v-model="form.quantity"
                                    required
                                    placeholder="Contoh: 100"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm font-semibold"
                                />
                                <InputError class="mt-1.5" :message="form.errors.quantity" />
                            </div>

                            <!-- Transaction Date -->
                            <div>
                                <label for="transaction_date" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Tanggal Masuk / Kedatangan <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="transaction_date"
                                    type="date"
                                    v-model="form.transaction_date"
                                    required
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.transaction_date" />
                            </div>

                            <!-- Supplier -->
                            <div>
                                <label for="supplier" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Vendor / Supplier Pengirim
                                </label>
                                <input
                                    id="supplier"
                                    type="text"
                                    v-model="form.supplier"
                                    placeholder="Nama CV / PT / Distributor"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.supplier" />
                            </div>

                            <!-- Condition -->
                            <div>
                                <label for="condition" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Kondisi Fisik Barang <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="condition"
                                    v-model="form.condition"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                    required
                                >
                                    <option value="Good">Good (Baik &amp; Segel Utuh)</option>
                                    <option value="Damaged">Damaged (Rusak / Cacat)</option>
                                </select>
                                <InputError class="mt-1.5" :message="form.errors.condition" />
                            </div>

                            <!-- Status -->
                            <div class="md:col-span-2">
                                <label for="status" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Status Pemrosesan Inbound <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                    required
                                >
                                    <option value="completed">Selesai (Completed - Stok gudang langsung bertambah)</option>
                                    <option value="pending">Tertunda (Pending - Menunggu verifikasi fisik lanjutan)</option>
                                </select>
                                <InputError class="mt-1.5" :message="form.errors.status" />
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Catatan Penerimaan / No. Surat Jalan Supplier
                                </label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="2"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm placeholder-gray-400"
                                    placeholder="Tuliskan nomor dokumen pengantar, no resi, atau catatan kondisi barang..."
                                ></textarea>
                                <InputError class="mt-1.5" :message="form.errors.notes" />
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Alokasi Lokasi Rak -->
                    <div class="space-y-6 pt-10">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600">
                                <PhMapPin class="w-5 h-5" weight="duotone" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold tracking-tight text-black">Alokasi ke Rak Penyimpanan</h3>
                                <p class="text-xs text-gray-500">Tentukan lokasi rak fisik tempat barang ini disimpan (opsional).</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Location ID -->
                            <div>
                                <label for="location_id" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Pilih Rak / Area Gudang
                                </label>
                                <select
                                    id="location_id"
                                    v-model="form.location_id"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                >
                                    <option value="">-- Pilih Rak (Opsional) --</option>
                                    <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                                        [{{ loc.code }}] {{ loc.name }} (Zona: {{ loc.zone_name || '-' }})
                                    </option>
                                </select>
                                <InputError class="mt-1.5" :message="form.errors.location_id" />
                            </div>

                            <!-- Location Quantity -->
                            <div v-if="form.location_id">
                                <label for="location_quantity" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Kuantitas Ditaruh di Rak Ini
                                </label>
                                <input
                                    id="location_quantity"
                                    type="number"
                                    min="1"
                                    :max="form.quantity"
                                    v-model="form.location_quantity"
                                    :placeholder="`Default: ${form.quantity}`"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.location_quantity" />
                            </div>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="flex items-center justify-between pt-8">
                        <Link
                            :href="route('inbound.index')"
                            class="px-6 py-3 text-sm font-semibold text-gray-600 hover:text-black transition-all rounded-full hover:bg-black/5"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center px-8 py-3.5 bg-terracotta-600 hover:bg-terracotta-700 text-white font-semibold text-sm rounded-full shadow-[0_8px_25px_rgba(193,83,53,0.25)] hover:scale-[1.02] active:scale-95 transition-all duration-300 disabled:opacity-50"
                        >
                            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Barang Masuk' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
