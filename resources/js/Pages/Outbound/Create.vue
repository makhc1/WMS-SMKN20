<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import InputError from '@/Components/InputError.vue';
import { PhArrowLeft, PhQrCode, PhTruck, PhListChecks, PhPackage, PhWarningCircle } from '@phosphor-icons/vue';
import { QrcodeStream } from 'vue-qrcode-reader';

const props = defineProps({
    items: Array,
    pickingLists: Array,
});

const form = useForm({
    item_id: '',
    transaction_date: new Date().toISOString().split('T')[0],
    quantity: 1,
    customer_name: '',
    customer_address: '',
    status: 'completed',
    courier: '',
    estimated_delivery_date: '',
    notes: '',
    picking_list_id: '',
});

const showScanner = ref(false);
const scannerError = ref('');

const onDetect = (detectedCodes) => {
    if (!detectedCodes || detectedCodes.length === 0) return;
    
    const decodedText = detectedCodes[0].rawValue;
    const foundItem = props.items.find(item => item.sku === decodedText);
    
    if (foundItem) {
        form.item_id = foundItem.id;
        showScanner.value = false;
        scannerError.value = '';
    } else {
        scannerError.value = `Barang dengan SKU "${decodedText}" tidak ditemukan.`;
    }
};

const onInit = async (promise) => {
    try {
        await promise;
    } catch (error) {
        if (error.name === 'NotAllowedError') {
            scannerError.value = 'Izin akses kamera ditolak.';
        } else if (error.name === 'NotFoundError') {
            scannerError.value = 'Tidak ada kamera yang terdeteksi.';
        } else {
            scannerError.value = 'Gagal memuat kamera: ' + error.message;
        }
    }
};

const selectedItem = computed(() => {
    if (!form.item_id) return null;
    return props.items.find(i => i.id === form.item_id);
});

const selectedPickingList = computed(() => {
    if (!form.picking_list_id) return null;
    return props.pickingLists.find(pl => pl.id == form.picking_list_id);
});

const selectPickingListItem = (item) => {
    form.item_id = item.id;
    form.quantity = item.pivot?.quantity || 1;
};

const submit = () => {
    form.post(route('outbound.store'));
};
</script>

<template>
    <Head title="Input Pengeluaran Barang (Outbound)" />

    <AuthenticatedLayout>
        <template #header>
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="route('outbound.index')" class="w-11 h-11 rounded-full bg-white hover:bg-black/5 flex items-center justify-center border border-black/10 text-gray-700 hover:text-black transition-all shadow-sm hover:scale-105 active:scale-95">
                        <PhArrowLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-black leading-tight">
                            Pengeluaran Barang (Outbound)
                        </h2>
                        <p class="text-xs text-gray-500 font-medium">Buat pesanan pengiriman barang dan kurangi kuantitas stok gudang.</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="max-w-4xl mx-auto pb-12">
            <div class="bg-white border border-black/10 rounded-3xl shadow-sm overflow-hidden">
                <form @submit.prevent="submit" class="p-8 sm:p-10 space-y-10 divide-y divide-black/5">
                    
                    <!-- Section 1: Pemilihan Barang / Picking List -->
                    <div class="space-y-6 pt-0">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                    <PhListChecks class="w-5 h-5" weight="duotone" />
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold tracking-tight text-black">Pemilihan Barang</h3>
                                    <p class="text-xs text-gray-500">Pilih dari Picking List atau pilih manual via katalog &amp; QR scanner.</p>
                                </div>
                            </div>

                            <button 
                                type="button" 
                                @click="showScanner = !showScanner" 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 text-black border border-black/10 rounded-full text-xs font-semibold shadow-sm transition-all hover:scale-105 active:scale-95"
                            >
                                <PhQrCode class="w-4 h-4 text-terracotta-600" />
                                <span>{{ showScanner ? 'Tutup Scanner' : 'Scan QR SKU' }}</span>
                            </button>
                        </div>

                        <!-- QR Scanner Viewfinder -->
                        <div v-if="showScanner" class="p-4 bg-black rounded-2xl relative overflow-hidden max-w-md mx-auto aspect-video">
                            <qrcode-stream @detect="onDetect" @camera-on="onInit"></qrcode-stream>
                            <div v-if="scannerError" class="absolute inset-0 flex items-center justify-center bg-black/85 text-white text-xs font-medium p-4 text-center">
                                {{ scannerError }}
                            </div>
                            <div class="absolute inset-0 border-2 border-dashed border-white/40 pointer-events-none m-6 rounded-xl"></div>
                        </div>

                        <div class="space-y-6">
                            <!-- Picking List Selection -->
                            <div>
                                <label for="picking_list_id" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Hubungkan dengan Picking List (Opsional)
                                </label>
                                <select
                                    id="picking_list_id"
                                    v-model="form.picking_list_id"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                >
                                    <option value="">-- Tanpa Picking List (Pilih Manual di bawah) --</option>
                                    <option v-for="pl in pickingLists" :key="pl.id" :value="pl.id">
                                        [{{ pl.code }}] • {{ pl.items?.length || 0 }} barang ({{ pl.status === 'completed' ? 'Selesai' : 'Pending' }})
                                    </option>
                                </select>
                            </div>

                            <!-- Expandable Picking List Items -->
                            <div v-if="selectedPickingList && selectedPickingList.items?.length > 0" class="pt-2 space-y-2">
                                <p class="text-xs font-semibold text-gray-500">Daftar Barang di Picking List (Klik untuk memilih):</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <button
                                        type="button"
                                        v-for="item in selectedPickingList.items" 
                                        :key="item.id"
                                        @click="selectPickingListItem(item)"
                                        :class="[
                                            'flex items-center justify-between p-3 rounded-xl border transition-all text-left',
                                            form.item_id == item.id 
                                                ? 'border-terracotta-600 bg-terracotta-50/50 shadow-sm' 
                                                : 'border-black/10 hover:bg-gray-50'
                                        ]"
                                    >
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded bg-black/5 flex items-center justify-center text-gray-700">
                                                <PhPackage class="w-3.5 h-3.5" />
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold text-black">{{ item.name }}</p>
                                                <p class="text-[10px] text-gray-400 font-mono">{{ item.sku }}</p>
                                            </div>
                                        </div>
                                        <span class="text-xs font-bold text-black">{{ item.pivot?.quantity || 0 }} {{ item.unit }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Manual Item Selection -->
                            <div>
                                <label for="item_id" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Pilih Barang Dari Master Katalog <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="item_id"
                                    v-model="form.item_id"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                    required
                                >
                                    <option value="" disabled>-- Pilih Barang --</option>
                                    <option v-for="item in items" :key="item.id" :value="item.id">
                                        [{{ item.sku }}] {{ item.name }} • Sisa Stok: {{ item.quantity }} {{ item.unit }}
                                    </option>
                                </select>
                                <InputError class="mt-1.5" :message="form.errors.item_id" />
                            </div>

                            <!-- Selected Item Snapshot Banner -->
                            <div v-if="selectedItem" class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 rounded-2xl bg-gray-50 border border-black/5 gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-terracotta-50 flex items-center justify-center text-terracotta-600">
                                        <PhPackage class="w-5 h-5" weight="duotone" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-black">{{ selectedItem.name }}</p>
                                        <p class="text-xs text-gray-400 font-mono">{{ selectedItem.sku }} • {{ selectedItem.category }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 text-right">
                                    <div>
                                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Stok Tersedia</p>
                                        <p :class="['text-base font-bold', selectedItem.quantity > 0 ? 'text-emerald-600' : 'text-red-600']">
                                            {{ selectedItem.quantity }} <span class="text-xs font-normal text-gray-500">{{ selectedItem.unit || 'Pcs' }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Data Pengiriman & Penerima -->
                    <div class="space-y-6 pt-10">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600">
                                <PhTruck class="w-5 h-5" weight="duotone" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold tracking-tight text-black">Detail Tujuan &amp; Ekspedisi</h3>
                                <p class="text-xs text-gray-500">Informasi nama penerima, alamat pengiriman, dan kurir.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Quantity -->
                            <div>
                                <label for="quantity" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Jumlah Barang Keluar <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="quantity"
                                    type="number"
                                    min="1"
                                    :max="selectedItem ? selectedItem.quantity : null"
                                    v-model="form.quantity"
                                    required
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-base font-semibold"
                                />
                                <p v-if="selectedItem && form.quantity > selectedItem.quantity" class="text-xs text-red-600 mt-1 font-medium flex items-center gap-1">
                                    <PhWarningCircle class="w-3.5 h-3.5" />
                                    Jumlah keluar melebihi sisa stok ({{ selectedItem.quantity }}).
                                </p>
                                <InputError class="mt-1.5" :message="form.errors.quantity" />
                            </div>

                            <!-- Transaction Date -->
                            <div>
                                <label for="transaction_date" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Tanggal Transaksi <span class="text-red-500">*</span>
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

                            <!-- Customer Name -->
                            <div>
                                <label for="customer_name" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Nama Penerima / Customer <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="customer_name"
                                    type="text"
                                    v-model="form.customer_name"
                                    required
                                    placeholder="Contoh: Lab Komputer / Bapak Budi"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.customer_name" />
                            </div>

                            <!-- Courier -->
                            <div>
                                <label for="courier" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Kurir / Ekspedisi Pengantar
                                </label>
                                <input
                                    id="courier"
                                    type="text"
                                    v-model="form.courier"
                                    placeholder="JNE / J&amp;T / Kurir Internal Gudang"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.courier" />
                            </div>

                            <!-- Customer Address -->
                            <div class="md:col-span-2">
                                <label for="customer_address" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Alamat Pengiriman / Lokasi Tujuan <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="customer_address"
                                    v-model="form.customer_address"
                                    rows="2"
                                    required
                                    placeholder="Tuliskan detail ruang/gedung/alamat penerima..."
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm placeholder-gray-400"
                                ></textarea>
                                <InputError class="mt-1.5" :message="form.errors.customer_address" />
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Status Pengeluaran <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                    required
                                >
                                    <option value="completed">Selesai (Completed - Stok gudang langsung berkurang)</option>
                                    <option value="pending">Tertunda (Pending - Menunggu proses packing/ambil)</option>
                                </select>
                                <InputError class="mt-1.5" :message="form.errors.status" />
                            </div>

                            <!-- Estimated Delivery Date -->
                            <div>
                                <label for="estimated_delivery_date" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Estimasi Tanggal Kirim / Tiba
                                </label>
                                <input
                                    id="estimated_delivery_date"
                                    type="date"
                                    v-model="form.estimated_delivery_date"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.estimated_delivery_date" />
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Catatan Pengeluaran / Keperluan
                                </label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="2"
                                    placeholder="Catatan keperluan proyek, tujuan barang, dll..."
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm placeholder-gray-400"
                                ></textarea>
                                <InputError class="mt-1.5" :message="form.errors.notes" />
                            </div>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="flex items-center justify-between pt-8">
                        <Link
                            :href="route('outbound.index')"
                            class="px-6 py-3 text-sm font-semibold text-gray-600 hover:text-black transition-all rounded-full hover:bg-black/5"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing || (selectedItem && form.quantity > selectedItem.quantity)"
                            class="inline-flex items-center justify-center px-8 py-3.5 bg-terracotta-600 hover:bg-terracotta-700 text-white font-semibold text-sm rounded-full shadow-[0_8px_25px_rgba(193,83,53,0.25)] hover:scale-[1.02] active:scale-95 transition-all duration-300 disabled:opacity-50"
                        >
                            <span>{{ form.processing ? 'Menyimpan...' : 'Buat Surat Jalan Pengeluaran' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
