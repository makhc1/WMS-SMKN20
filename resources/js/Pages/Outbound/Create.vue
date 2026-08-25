<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { PhArrowLeft, PhQrCode, PhTruck } from '@phosphor-icons/vue';
import { QrcodeStream } from 'vue-qrcode-reader';

const props = defineProps({
    items: Array
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
    notes: ''
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
        try { new Audio('data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU').play(); } catch (e) {}
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

const submit = () => {
    form.post(route('outbound.store'));
};
</script>

<template>
    <Head title="Input Barang Keluar" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link :href="route('outbound.index')" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-600 hover:text-black transition-colors shadow-sm border border-black/5">
                    <PhArrowLeft class="w-5 h-5" />
                </Link>
                <h2 class="text-2xl font-bold tracking-tighter text-black">
                    Input Barang Keluar
                </h2>
            </div>
        </template>

        <div class="max-w-4xl">
            <div class="bg-white border-r border-black/10">
                <div class="bg-transparent  overflow-hidden p-8 md:p-12">
                    
                    <form @submit.prevent="submit" class="space-y-8">
                        
                        <!-- Scanner & Picking List Section -->
                        <div class="bg-gray-50 rounded-[1.5rem] p-6 border border-black/5">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-sm font-bold tracking-tight text-black">Picking List (Pilih Barang)</h3>
                                    <p class="text-xs text-gray-600 mt-1">Pilih manual atau scan QR Code label.</p>
                                </div>
                                <button type="button" @click="showScanner = !showScanner" class="flex items-center gap-3 px-6 py-4 bg-white rounded-full text-xs font-semibold text-black shadow-sm hover:bg-gray-50 transition-colors border border-black/5">
                                    <PhQrCode class="w-4 h-4" />
                                    {{ showScanner ? 'Tutup Scanner' : 'Scan QR' }}
                                </button>
                            </div>

                            <!-- Scanner Viewfinder -->
                            <div v-if="showScanner" class="mb-6 rounded-2xl overflow-hidden bg-black aspect-video relative max-w-md mx-auto ring-4 ring-terracotta-600/30">
                                <qrcode-stream @detect="onDetect" @camera-on="onInit"></qrcode-stream>
                                <div v-if="scannerError" class="absolute inset-0 flex items-center justify-center bg-black/80 text-white text-xs font-medium p-6 sm:p-8 text-center">
                                    {{ scannerError }}
                                </div>
                                <div class="absolute inset-0 border-2 border-dashed border-white/50 pointer-events-none m-8 rounded-xl opacity-50"></div>
                            </div>

                            <!-- Select Item -->
                            <div>
                                <select
                                    id="item_id"
                                    v-model="form.item_id"
                                    class="block w-full px-6 py-4 border border-black/10 rounded-xl bg-white text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all"
                                    required
                                >
                                    <option value="" disabled>-- Pilih Barang --</option>
                                    <option v-for="item in items" :key="item.id" :value="item.id">
                                        [{{ item.sku }}] {{ item.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.item_id" class="mt-2 text-sm text-red-600">{{ form.errors.item_id }}</div>
                            </div>

                            <!-- Selected Item Details -->
                            <div v-if="selectedItem" class="mt-4 flex flex-col md:flex-row items-start md:items-center justify-between bg-white p-6 sm:p-8 rounded-xl border border-black/5 gap-6">
                                <div class="text-sm flex-1">
                                    <p class="text-gray-600 text-xs">SKU</p>
                                    <p class="font-mono text-black font-semibold">{{ selectedItem.sku }}</p>
                                </div>
                                <div class="text-sm flex-1">
                                    <p class="text-gray-600 text-xs">Lokasi Rak</p>
                                    <p class="font-medium text-black">{{ selectedItem.location || 'Tidak diset' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-600">Stok Tersedia</p>
                                    <p class="text-xl font-bold" :class="selectedItem.quantity > 0 ? 'text-green-600' : 'text-red-600'">
                                        {{ selectedItem.quantity }} <span class="text-sm font-normal text-gray-600">{{ selectedItem.unit || 'Pcs' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Quantity -->
                            <div class="md:col-span-2">
                                <label for="quantity" class="block text-sm font-medium text-gray-700">Jumlah Keluar</label>
                                <div class="flex items-center gap-6 mt-2">
                                    <input
                                        id="quantity"
                                        type="number"
                                        min="1"
                                        :max="selectedItem ? selectedItem.quantity : null"
                                        v-model="form.quantity"
                                        required
                                        class="block w-full md:w-1/3 px-6 py-4 border border-black/10 rounded-2xl bg-white text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all"
                                    />
                                    <span v-if="selectedItem" class="text-sm font-medium text-gray-600">{{ selectedItem.unit || 'Pcs' }}</span>
                                </div>
                                <div v-if="form.errors.quantity" class="mt-2 text-sm text-red-600">{{ form.errors.quantity }}</div>
                            </div>

                            <!-- Customer Name -->
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700">Nama Customer / Penerima</label>
                                <input
                                    id="customer_name"
                                    type="text"
                                    v-model="form.customer_name"
                                    required
                                    class="mt-2 block w-full px-6 py-4 border border-black/10 rounded-2xl bg-white text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all"
                                />
                                <div v-if="form.errors.customer_name" class="mt-2 text-sm text-red-600">{{ form.errors.customer_name }}</div>
                            </div>

                            <!-- Transaction Date -->
                            <div>
                                <label for="transaction_date" class="block text-sm font-medium text-gray-700">Tanggal Transaksi</label>
                                <input
                                    id="transaction_date"
                                    type="date"
                                    v-model="form.transaction_date"
                                    required
                                    class="mt-2 block w-full px-6 py-4 border border-black/10 rounded-2xl bg-white text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all"
                                />
                                <div v-if="form.errors.transaction_date" class="mt-2 text-sm text-red-600">{{ form.errors.transaction_date }}</div>
                            </div>

                            <!-- Customer Address -->
                            <div class="md:col-span-2">
                                <label for="customer_address" class="block text-sm font-medium text-gray-700">Alamat Pengiriman</label>
                                <textarea
                                    id="customer_address"
                                    class="mt-2 block w-full px-6 py-4 border border-black/10 rounded-2xl bg-white text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all"
                                    rows="2"
                                    v-model="form.customer_address"
                                    required
                                ></textarea>
                                <div v-if="form.errors.customer_address" class="mt-2 text-sm text-red-600">{{ form.errors.customer_address }}</div>
                            </div>
                            
                            <!-- Courier -->
                            <div>
                                <label for="courier" class="block text-sm font-medium text-gray-700 flex items-center gap-3">
                                    <PhTruck class="w-4 h-4 text-gray-600" /> Ekspedisi / Kurir
                                </label>
                                <input
                                    id="courier"
                                    type="text"
                                    v-model="form.courier"
                                    placeholder="JNE / J&T / Kurir Internal"
                                    class="mt-2 block w-full px-6 py-4 border border-black/10 rounded-2xl bg-white text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all"
                                />
                                <div v-if="form.errors.courier" class="mt-2 text-sm text-red-600">{{ form.errors.courier }}</div>
                            </div>
                            
                            <!-- Status -->
                            <div class="md:col-span-2">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status Pemrosesan</label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-2 block w-full px-6 py-4 border border-black/10 rounded-2xl bg-white text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all"
                                    required
                                >
                                    <option value="completed">Selesai (Stok akan langsung berkurang)</option>
                                    <option value="pending">Tertunda / Pending (Stok belum berkurang)</option>
                                </select>
                                <div v-if="form.errors.status" class="mt-2 text-sm text-red-600">{{ form.errors.status }}</div>
                            </div>

                            <!-- Estimated Delivery -->
                            <div>
                                <label for="estimated_delivery_date" class="block text-sm font-medium text-gray-700">Estimasi Tanggal Kirim</label>
                                <input
                                    id="estimated_delivery_date"
                                    type="date"
                                    v-model="form.estimated_delivery_date"
                                    class="mt-2 block w-full px-6 py-4 border border-black/10 rounded-2xl bg-white text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all"
                                />
                                <div v-if="form.errors.estimated_delivery_date" class="mt-2 text-sm text-red-600">{{ form.errors.estimated_delivery_date }}</div>
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Catatan Tambahan</label>
                                <textarea
                                    id="notes"
                                    class="mt-2 block w-full px-6 py-4 border border-black/10 rounded-2xl bg-white text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all"
                                    rows="2"
                                    v-model="form.notes"
                                ></textarea>
                                <div v-if="form.errors.notes" class="mt-2 text-sm text-red-600">{{ form.errors.notes }}</div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-4 gap-6">
                            <Link
                                :href="route('outbound.index')"
                                class="px-6 py-3 text-sm font-semibold text-gray-600 hover:text-black transition-colors"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                :class="{'opacity-50 cursor-not-allowed': form.processing || (selectedItem && form.quantity > selectedItem.quantity)}"
                                :disabled="form.processing || (selectedItem && form.quantity > selectedItem.quantity)"
                                class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-full text-sm font-semibold text-white bg-terracotta-600 hover:bg-terracotta-700 transition-all duration-700 shadow-[0_8px_30px_rgb(193,83,53,0.3)] hover:scale-[0.98]"
                            >
                                Buat Pengiriman (Surat Jalan)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
