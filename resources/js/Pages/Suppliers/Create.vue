<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import { PhArrowLeft, PhTruck, PhSparkle } from '@phosphor-icons/vue';

const form = useForm({
    supplier_id: '',
    nama_supplier: '',
    telepon: '',
    alamat: '',
});

const generateId = () => {
    const random = Math.floor(1000 + Math.random() * 9000);
    form.supplier_id = `SUP-${random}`;
};

const submit = () => {
    form.post(route('suppliers.store'));
};
</script>

<template>
    <Head title="Tambah Supplier" />

    <AuthenticatedLayout>
        <template #header>
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="route('suppliers.index')" class="w-11 h-11 rounded-full bg-white hover:bg-black/5 flex items-center justify-center border border-black/10 text-gray-700 hover:text-black transition-all shadow-sm hover:scale-105 active:scale-95">
                        <PhArrowLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-black leading-tight">
                            Tambah Supplier Baru
                        </h2>
                        <p class="text-xs text-gray-500 font-medium">Lengkapi rincian data supplier barang.</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="max-w-4xl mx-auto pb-12">
            <div class="bg-white border border-black/10 rounded-3xl shadow-sm overflow-hidden">
                <form @submit.prevent="submit" class="p-8 sm:p-10 space-y-10 divide-y divide-black/5">
                    
                    <!-- Section 2: Informasi Dasar -->
                    <div class="space-y-6 pt-0">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                <PhTruck class="w-5 h-5" weight="duotone" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold tracking-tight text-black">Informasi Supplier</h3>
                                <p class="text-xs text-gray-500">Data identitas supplier.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Supplier ID -->
                            <div>
                                <label for="supplier_id" class="block text-xs font-semibold text-gray-700 mb-2">
                                    ID Supplier <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <input
                                        id="supplier_id"
                                        type="text"
                                        v-model="form.supplier_id"
                                        required
                                        placeholder="SUP-XXXX"
                                        class="flex-1 px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm font-mono uppercase"
                                    />
                                    <button 
                                        type="button" 
                                        @click="generateId" 
                                        class="inline-flex items-center gap-1.5 px-4 py-3 bg-black hover:bg-gray-800 text-white text-xs font-semibold rounded-xl transition-all active:scale-95 shadow-sm"
                                    >
                                        <PhSparkle class="w-3.5 h-3.5" />
                                        <span>Auto</span>
                                    </button>
                                </div>
                                <InputError class="mt-1.5" :message="form.errors.supplier_id" />
                            </div>

                            <!-- Name -->
                            <div>
                                <label for="nama_supplier" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Nama Supplier <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="nama_supplier"
                                    type="text"
                                    v-model="form.nama_supplier"
                                    required
                                    placeholder="Contoh: PT ABC Makmur"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.nama_supplier" />
                            </div>

                            <!-- Telepon -->
                            <div>
                                <label for="telepon" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Telepon
                                </label>
                                <input
                                    id="telepon"
                                    type="text"
                                    v-model="form.telepon"
                                    placeholder="Contoh: 08123456789"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.telepon" />
                            </div>

                            <!-- Alamat -->
                            <div class="md:col-span-2">
                                <label for="alamat" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Alamat
                                </label>
                                <textarea
                                    id="alamat"
                                    v-model="form.alamat"
                                    rows="3"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm placeholder-gray-400"
                                    placeholder="Tuliskan alamat lengkap supplier..."
                                ></textarea>
                                <InputError class="mt-1.5" :message="form.errors.alamat" />
                            </div>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="flex items-center justify-between pt-8">
                        <Link
                            :href="route('suppliers.index')"
                            class="px-6 py-3 text-sm font-semibold text-gray-600 hover:text-black transition-all rounded-full hover:bg-black/5"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center px-8 py-3.5 bg-terracotta-600 hover:bg-terracotta-700 text-white font-semibold text-sm rounded-full shadow-[0_8px_25px_rgba(193,83,53,0.25)] hover:scale-[1.02] active:scale-95 transition-all duration-300 disabled:opacity-50"
                        >
                            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Supplier' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
