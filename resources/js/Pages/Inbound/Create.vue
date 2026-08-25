<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { PhArrowLeft, PhQrCode } from '@phosphor-icons/vue';
import { QrcodeStream } from 'vue-qrcode-reader';

const props = defineProps({
    items: Array,
});

const form = useForm({
    item_id: '',
    transaction_date: new Date().toISOString().split('T')[0],
    quantity: 1,
    supplier: '',
    condition: 'Good',
    status: 'completed',
    notes: '',
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

const submit = () => {
    form.post(route('inbound.store'));
};
</script>

<template>
    <Head title="Input Inbound" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link :href="route('inbound.index')" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-600 hover:text-black transition-colors shadow-sm border border-black/5">
                    <PhArrowLeft class="w-5 h-5" />
                </Link>
                <h2 class="text-2xl font-bold tracking-tighter text-black">
                    Input Barang Masuk
                </h2>
            </div>
        </template>

        <div class="max-w-4xl">
            <div class="bg-white border-r border-black/10">
                <div class="bg-transparent  overflow-hidden p-8 md:p-12">
                    
                    <form @submit.prevent="submit" class="space-y-8">
                        
                        <!-- Scanner Section -->
                        <div class="bg-gray-50 rounded-[1.5rem] p-6 border border-black/5">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-sm font-bold tracking-tight text-black">Pilih Barang</h3>
                                    <p class="text-xs text-gray-600 mt-1">Pilih manual dari daftar atau scan QR Code label.</p>
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
                                <InputError class="mt-2" :message="form.errors.item_id" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Quantity -->
                            <div>
                                <InputLabel for="quantity" value="Jumlah Diterima" />
                                <TextInput
                                    id="quantity"
                                    type="number"
                                    min="1"
                                    class="w-full mt-1"
                                    v-model="form.quantity"
                                    required
                                    placeholder="Contoh: 50"
                                />
                                <InputError class="mt-2" :message="form.errors.quantity" />
                            </div>

                            <!-- Transaction Date -->
                            <div>
                                <InputLabel for="transaction_date" value="Tanggal Kedatangan" />
                                <TextInput
                                    id="transaction_date"
                                    type="date"
                                    class="w-full mt-1"
                                    v-model="form.transaction_date"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.transaction_date" />
                            </div>

                            <!-- Supplier -->
                            <div>
                                <InputLabel for="supplier" value="Supplier / Asal Barang" />
                                <TextInput
                                    id="supplier"
                                    type="text"
                                    class="w-full mt-1"
                                    v-model="form.supplier"
                                    placeholder="Nama Vendor / Toko"
                                />
                                <InputError class="mt-2" :message="form.errors.supplier" />
                            </div>

                            <!-- Condition -->
                            <div>
                                <InputLabel for="condition" value="Kondisi Barang" />
                                <select
                                    id="condition"
                                    v-model="form.condition"
                                    class="mt-1 block w-full px-6 py-4 border border-black/10 rounded-xl bg-white text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all"
                                    required
                                >
                                    <option value="Good">Good (Baik)</option>
                                    <option value="Damaged">Damaged (Rusak/Cacat)</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.condition" />
                            </div>

                            <!-- Status -->
                            <div>
                                <InputLabel for="status" value="Status Pemrosesan" />
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-1 block w-full px-6 py-4 border border-black/10 rounded-xl bg-white text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all"
                                    required
                                >
                                    <option value="completed">Selesai (Stok akan langsung bertambah)</option>
                                    <option value="pending">Tertunda / Pending (Stok belum bertambah)</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <InputLabel for="notes" value="Catatan Tambahan" />
                                <textarea
                                    id="notes"
                                    class="block w-full mt-1 px-6 py-4 border border-black/10 rounded-2xl bg-white text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all"
                                    rows="3"
                                    v-model="form.notes"
                                    placeholder="Keterangan kondisi barang jika rusak, dsb..."
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.notes" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-4 gap-6">
                            <Link
                                :href="route('inbound.index')"
                                class="px-6 py-3 text-sm font-semibold text-gray-600 hover:text-black transition-colors"
                            >
                                Batal
                            </Link>
                            <PrimaryButton
                                class="!rounded-full px-8 py-3 shadow-[0_8px_20px_rgb(211,106,73,0.25)] hover:shadow-[0_8px_25px_rgb(211,106,73,0.4)] transition-all duration-300"
                                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                                :disabled="form.processing"
                            >
                                Simpan Inbound
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
