<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { PhArrowLeft, PhImageSquare } from '@phosphor-icons/vue';
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
    _method: 'put', // Spoofing PUT for file upload support in Laravel
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

const submit = () => {
    // Because of file upload, we must use POST with _method=put
    form.post(route('items.update', props.item.id));
};
</script>

<template>
    <Head title="Edit Barang" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-6">
                <Link :href="route('items.index')" class="p-2 -ml-2 rounded-full hover:bg-black/5 text-gray-600 hover:text-black transition-colors">
                    <PhArrowLeft class="w-5 h-5" />
                </Link>
                <h2 class="text-xl font-semibold tracking-tight text-black">
                    Edit Barang: {{ item.name }}
                </h2>
            </div>
        </template>

        <div class="max-w-4xl bg-white border border-black/10 rounded-[1.5rem] overflow-hidden">
            <div class="p-8">
                <form @submit.prevent="submit" class="space-y-8">
                    
                    <!-- Photo Upload -->
                    <div>
                        <InputLabel value="Foto Produk" class="mb-3" />
                        <div class="flex items-start gap-6">
                            <div class="w-32 h-32 rounded-2xl border-2 border-dashed border-black/10 flex items-center justify-center bg-black/[0.01] overflow-hidden group cursor-pointer hover:bg-black/[0.03] transition-colors" @click="triggerFileInput">
                                <img v-if="photoPreview" :src="photoPreview" alt="Preview" class="w-full h-full object-cover" />
                                <div v-else class="flex flex-col items-center justify-center text-gray-600 group-hover:text-black transition-colors">
                                    <PhImageSquare class="w-8 h-8 mb-2" />
                                    <span class="text-xs font-medium uppercase tracking-wider">Upload</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="handlePhotoChange" />
                                <PrimaryButton type="button" @click="triggerFileInput" class="!bg-white !text-black border border-black/10 hover:!bg-black/5 mb-2 shadow-sm">
                                    Ganti Gambar
                                </PrimaryButton>
                                <p class="text-xs text-gray-600">Kosongkan jika tidak ingin mengubah foto. Maks 2MB.</p>
                                <InputError class="mt-2" :message="form.errors.photo" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- SKU -->
                        <div>
                            <InputLabel for="sku" value="SKU / Kode Barang" />
                            <TextInput
                                id="sku"
                                type="text"
                                class="mt-1 block w-full bg-gray-50 text-gray-600 cursor-not-allowed"
                                v-model="form.sku"
                                readonly
                            />
                            <InputError class="mt-2" :message="form.errors.sku" />
                        </div>

                        <!-- Name -->
                        <div>
                            <InputLabel for="name" value="Nama Produk" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <!-- Category -->
                        <div>
                            <InputLabel for="category" value="Kategori" />
                            <TextInput
                                id="category"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.category"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.category" />
                        </div>

                        <!-- Unit -->
                        <div>
                            <InputLabel for="unit" value="Satuan" />
                            <select
                                id="unit"
                                v-model="form.unit"
                                class="mt-1 block w-full border-gray-300 focus:border-terracotta-500 focus:ring-terracotta-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="Pcs">Pcs</option>
                                <option value="Box">Box</option>
                                <option value="Kg">Kg</option>
                                <option value="Lusin">Lusin</option>
                                <option value="Rim">Rim</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.unit" />
                        </div>

                        <!-- Base Price -->
                        <div>
                            <InputLabel for="base_price" value="Harga Dasar (Rp)" />
                            <TextInput
                                id="base_price"
                                type="number"
                                min="0"
                                class="mt-1 block w-full"
                                v-model="form.base_price"
                            />
                            <InputError class="mt-2" :message="form.errors.base_price" />
                        </div>

                        <!-- Threshold -->
                        <div>
                            <InputLabel for="low_stock_threshold" value="Batas Stok Minimal (Reorder Point)" />
                            <TextInput
                                id="low_stock_threshold"
                                type="number"
                                min="0"
                                class="mt-1 block w-full"
                                v-model="form.low_stock_threshold"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.low_stock_threshold" />
                        </div>

                        <!-- Quantity (Stock Adjustment) -->
                        <div v-if="canAdjustStock">
                            <InputLabel for="quantity" value="Total Stok (Hanya Admin / Manager)" />
                            <TextInput
                                id="quantity"
                                type="number"
                                min="0"
                                class="mt-1 block w-full"
                                v-model="form.quantity"
                            />
                            <p class="text-xs text-amber-600 mt-1">*Ubah hanya jika terjadi selisih (Stock Opname).</p>
                            <InputError class="mt-2" :message="form.errors.quantity" />
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <InputLabel for="description" value="Deskripsi Produk" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="mt-1 block w-full border-gray-300 focus:border-terracotta-500 focus:ring-terracotta-500 rounded-md shadow-sm"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-6 pt-6 border-t border-black/5">
                        <Link
                            :href="route('items.index')"
                            class="px-6 py-2.5 text-sm font-semibold text-gray-600 hover:text-black transition-colors rounded-full"
                        >
                            Batal
                        </Link>
                        <PrimaryButton
                            class="!rounded-full px-8 py-3 shadow-[0_8px_20px_rgb(211,106,73,0.25)] hover:shadow-[0_8px_25px_rgb(211,106,73,0.4)] transition-all duration-300"
                            :class="{ 'opacity-50': form.processing }"
                            :disabled="form.processing"
                        >
                            Simpan Perubahan
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
