<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import { PhArrowLeft, PhMapPin, PhSlidersHorizontal } from '@phosphor-icons/vue';

const props = defineProps({
    location: Object,
});

const form = useForm({
    code: props.location.code,
    name: props.location.name,
    zone_name: props.location.zone_name || '',
    storage_type: props.location.storage_type || 'Dry',
    capacity_percentage: props.location.capacity_percentage || 0,
    status: props.location.status || 'Active',
});

const submit = () => {
    form.put(route('locations.update', props.location.id));
};
</script>

<template>
    <Head :title="`Edit Lokasi - ${location.code}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="route('locations.index')" class="w-11 h-11 rounded-full bg-white hover:bg-black/5 flex items-center justify-center border border-black/10 text-gray-700 hover:text-black transition-all shadow-sm hover:scale-105 active:scale-95">
                        <PhArrowLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-black leading-tight">
                            Edit Lokasi / Rak
                        </h2>
                        <p class="text-xs text-gray-500 font-medium">Perbarui spesifikasi rak: [{{ location.code }}] {{ location.name }}</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="max-w-4xl mx-auto pb-12">
            <div class="bg-white border border-black/10 rounded-3xl shadow-sm overflow-hidden">
                <form @submit.prevent="submit" class="p-8 sm:p-10 space-y-10 divide-y divide-black/5">
                    
                    <!-- Section 1: Informasi Identitas Rak -->
                    <div class="space-y-6 pt-0">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600">
                                <PhMapPin class="w-5 h-5" weight="duotone" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold tracking-tight text-black">Identitas Lokasi &amp; Rak</h3>
                                <p class="text-xs text-gray-500">Kode rak dan nama zona gedung penyimpanan.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Code -->
                            <div>
                                <label for="code" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Kode Rak / Slot <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="code"
                                    type="text"
                                    v-model="form.code"
                                    required
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm font-mono uppercase"
                                />
                                <InputError class="mt-1.5" :message="form.errors.code" />
                            </div>

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Nama / Deskripsi Rak <span class="text-red-500">*</span>
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

                            <!-- Zone Name -->
                            <div class="md:col-span-2">
                                <label for="zone_name" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Zona / Blok Gudang
                                </label>
                                <input
                                    id="zone_name"
                                    type="text"
                                    v-model="form.zone_name"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.zone_name" />
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Spesifikasi & Kapasitas -->
                    <div class="space-y-6 pt-10">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                <PhSlidersHorizontal class="w-5 h-5" weight="duotone" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold tracking-tight text-black">Spesifikasi &amp; Operasional</h3>
                                <p class="text-xs text-gray-500">Tipe ruangan penyimpanan, persentase kapasitas, dan status rak.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Storage Type -->
                            <div>
                                <label for="storage_type" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Tipe Penyimpanan <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="storage_type"
                                    v-model="form.storage_type"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                    required
                                >
                                    <option value="Dry">Dry (Kering / Umum)</option>
                                    <option value="Cold Storage">Cold Storage (Berpendingin)</option>
                                    <option value="Hazardous">Hazardous (Bahan Khusus)</option>
                                </select>
                                <InputError class="mt-1.5" :message="form.errors.storage_type" />
                            </div>

                            <!-- Capacity Percentage -->
                            <div>
                                <label for="capacity_percentage" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Estimasi Terisi (%) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="capacity_percentage"
                                    type="number"
                                    min="0"
                                    max="100"
                                    v-model="form.capacity_percentage"
                                    required
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.capacity_percentage" />
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Status Rak <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                    required
                                >
                                    <option value="Active">Active (Siap Digunakan)</option>
                                    <option value="Under Maintenance">Under Maintenance (Perbaikan)</option>
                                </select>
                                <InputError class="mt-1.5" :message="form.errors.status" />
                            </div>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="flex items-center justify-between pt-8">
                        <Link
                            :href="route('locations.index')"
                            class="px-6 py-3 text-sm font-semibold text-gray-600 hover:text-black transition-all rounded-full hover:bg-black/5"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center px-8 py-3.5 bg-terracotta-600 hover:bg-terracotta-700 text-white font-semibold text-sm rounded-full shadow-[0_8px_25px_rgba(193,83,53,0.25)] hover:scale-[1.02] active:scale-95 transition-all duration-300 disabled:opacity-50"
                        >
                            <span>{{ form.processing ? 'Menyimpan...' : 'Perbarui Lokasi Rak' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
