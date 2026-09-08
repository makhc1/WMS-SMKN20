<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import { PhArrowLeft, PhCube, PhTruck, PhMapPin, PhSparkle, PhImageSquare, PhTrash, PhPlus } from '@phosphor-icons/vue';

const props = defineProps({
    locations: Array,
});

const newRow = () => ({
    sku: '',
    name: '',
    category: '',
    unit: 'Pcs',
    base_price: '',
    description: '',
    photo: null,
    photos: [],
    photoPreview: null,
    quantity: 1,
    condition: 'Good',
    location_id: '',
    location_quantity: '',
});

const form = useForm({
    transaction_date: new Date().toISOString().split('T')[0],
    supplier: '',
    status: 'completed',
    notes: '',
    items: [newRow()],
});

const addItem = () => {
    form.items.push(newRow());
};

const removeItem = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const generateSku = (item) => {
    const random = Math.random().toString(36).substring(2, 7).toUpperCase();
    item.sku = `ITM-${random}`;
};

const handlePhotoChange = (e, item) => {
    const file = e.target.files[0];
    if (file) {
        item.photo = file;
        const reader = new FileReader();
        reader.onload = (ev) => {
            item.photoPreview = ev.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removePhoto = (item) => {
    item.photo = null;
    item.photoPreview = null;
};

const triggerFileInput = (index) => {
    const input = document.getElementById(`photo-input-${index}`);
    if (input) input.click();
};

const submit = () => {
    if (form.items.length === 0) {
        form.items.push(newRow());
    }
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

                    <!-- Section: Data Penerimaan (shared) -->
                    <div class="space-y-6 pt-0">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                                <PhTruck class="w-5 h-5" weight="duotone" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold tracking-tight text-black">Detail Transaksi Kedatangan</h3>
                                <p class="text-xs text-gray-500">Berlaku untuk semua barang yang diterima pada transaksi ini.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                    <!-- Section: List Barang Masuk -->
                    <div class="space-y-6 pt-10">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                                <PhCube class="w-5 h-5" weight="duotone" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold tracking-tight text-black">Daftar Barang Diterima</h3>
                                <p class="text-xs text-gray-500">Isi satu atau lebih barang sekaligus. Jika SKU sudah terdaftar, stok otomatis ditambahkan.</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div v-for="(item, index) in form.items" :key="index" class="border border-black/10 rounded-3xl p-5 sm:p-6 relative">
                                <InputError class="mb-3" :message="form.errors[`items.${index}.sku`]" />
                                <InputError class="mb-3" :message="form.errors[`items.${index}.name`]" />
                                <InputError class="mb-3" :message="form.errors[`items.${index}.category`]" />
                                <InputError class="mb-3" :message="form.errors[`items.${index}.quantity`]" />

                                <div class="flex items-center justify-between mb-5">
                                    <span class="inline-flex items-center gap-2 text-xs font-bold text-gray-600 uppercase tracking-wider">
                                        <span class="w-7 h-7 rounded-full bg-black text-white flex items-center justify-center text-xs">{{ index + 1 }}</span>
                                        Barang #{{ index + 1 }}
                                    </span>
                                    <button
                                        type="button"
                                        v-if="form.items.length > 1"
                                        @click="removeItem(index)"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 rounded-xl transition-all"
                                    >
                                        <PhTrash class="w-4 h-4" />
                                        Hapus
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- SKU -->
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                                            SKU / Barcode Barang <span class="text-red-500">*</span>
                                        </label>
                                        <div class="flex gap-2">
                                            <input
                                                type="text"
                                                v-model="item.sku"
                                                required
                                                placeholder="ITM-XXXX"
                                                class="flex-1 px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm font-mono uppercase"
                                            />
                                            <button
                                                type="button"
                                                @click="generateSku(item)"
                                                class="inline-flex items-center gap-1.5 px-4 py-3 bg-black hover:bg-gray-800 text-white text-xs font-semibold rounded-xl transition-all active:scale-95 shadow-sm"
                                            >
                                                <PhSparkle class="w-3.5 h-3.5" />
                                                <span>Auto</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Name -->
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                                            Nama Barang <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            v-model="item.name"
                                            required
                                            placeholder="Nama barang diterima"
                                            class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                        />
                                    </div>

                                    <!-- Category -->
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                                            Kategori <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            v-model="item.category"
                                            required
                                            placeholder="Elektronik, ATK, Perkakas, dll"
                                            class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                        />
                                    </div>

                                    <!-- Unit -->
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                                            Satuan Unit <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            v-model="item.unit"
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
                                    </div>

                                    <!-- Base Price -->
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                                            Harga Satuan Dasar (Rp)
                                        </label>
                                        <input
                                            type="number"
                                            min="0"
                                            v-model="item.base_price"
                                            placeholder="Contoh: 25000"
                                            class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                        />
                                    </div>

                                    <!-- Quantity -->
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                                            Jumlah Diterima <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="number"
                                            min="1"
                                            v-model="item.quantity"
                                            required
                                            placeholder="Contoh: 100"
                                            class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm font-semibold"
                                        />
                                    </div>

                                    <!-- Condition -->
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                                            Kondisi Fisik Barang <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            v-model="item.condition"
                                            class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                            required
                                        >
                                            <option value="Good">Good (Baik &amp; Segel Utuh)</option>
                                            <option value="Damaged">Damaged (Rusak / Cacat)</option>
                                        </select>
                                    </div>

                                    <!-- Location ID -->
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                                            Pilih Rak / Area Gudang
                                        </label>
                                        <select
                                            v-model="item.location_id"
                                            class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                        >
                                            <option value="">-- Pilih Rak (Opsional) --</option>
                                            <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                                                [{{ loc.code }}] {{ loc.name }} (Zona: {{ loc.zone_name || '-' }})
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Location Quantity -->
                                    <div v-if="item.location_id">
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                                            Kuantitas Ditaruh di Rak Ini
                                        </label>
                                        <input
                                            type="number"
                                            min="1"
                                            :max="item.quantity"
                                            v-model="item.location_quantity"
                                            :placeholder="`Default: ${item.quantity}`"
                                            class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                        />
                                    </div>

                                    <!-- Photo Upload -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                                            Foto Barang (Opsional)
                                        </label>
                                        <div class="flex items-center gap-3">
                                            <input
                                                :id="`photo-input-${index}`"
                                                type="file"
                                                class="hidden"
                                                accept="image/*"
                                                @change="handlePhotoChange($event, item)"
                                            />
                                            <button
                                                type="button"
                                                @click="triggerFileInput(index)"
                                                class="px-4 py-2.5 bg-white hover:bg-gray-50 text-black border border-black/10 rounded-xl text-xs font-semibold shadow-sm transition-all flex items-center gap-2"
                                            >
                                                <PhImageSquare class="w-4 h-4 text-terracotta-600" />
                                                <span>{{ item.photoPreview ? 'Ganti Foto' : 'Upload Foto' }}</span>
                                            </button>
                                            <button
                                                v-if="item.photoPreview"
                                                type="button"
                                                @click="removePhoto(item)"
                                                class="p-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition-all"
                                            >
                                                <PhTrash class="w-4 h-4" />
                                            </button>
                                            <span v-if="item.photoPreview" class="text-xs font-medium text-emerald-600">Foto dipilih</span>
                                            <InputError :message="form.errors[`items.${index}.photo`]" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Add item button -->
                            <button
                                type="button"
                                @click="addItem"
                                class="w-full py-4 rounded-2xl border-2 border-dashed border-black/15 hover:border-terracotta-500 hover:bg-terracotta-50/40 text-gray-500 hover:text-terracotta-600 text-sm font-semibold transition-all flex items-center justify-center gap-2"
                            >
                                <PhPlus class="w-5 h-5" weight="bold" />
                                Tambah Barang Lain
                            </button>
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
                            <span>{{ form.processing ? 'Menyimpan...' : `Simpan ${form.items.length} Barang` }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
