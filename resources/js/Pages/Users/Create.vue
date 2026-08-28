<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import { PhArrowLeft, PhUser, PhKey } from '@phosphor-icons/vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'Staff Picker',
    status: 'Active',
});

const submit = () => {
    form.post(route('users.store'));
};
</script>

<template>
    <Head title="Tambah Pengguna" />

    <AuthenticatedLayout>
        <template #header>
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="route('users.index')" class="w-11 h-11 rounded-full bg-white hover:bg-black/5 flex items-center justify-center border border-black/10 text-gray-700 hover:text-black transition-all shadow-sm hover:scale-105 active:scale-95">
                        <PhArrowLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-black leading-tight">
                            Tambah Pengguna Baru
                        </h2>
                        <p class="text-xs text-gray-500 font-medium">Buat akun untuk staf atau admin gudang baru.</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="max-w-4xl mx-auto pb-12">
            <div class="bg-white border border-black/10 rounded-3xl shadow-sm overflow-hidden">
                <form @submit.prevent="submit" class="p-8 sm:p-10 space-y-10 divide-y divide-black/5">
                    
                    <!-- Section 1: Informasi Profil & Akses -->
                    <div class="space-y-6 pt-0">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                <PhUser class="w-5 h-5" weight="duotone" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold tracking-tight text-black">Informasi Akun</h3>
                                <p class="text-xs text-gray-500">Nama lengkap, email untuk login, dan role pengguna.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="name"
                                    type="text"
                                    v-model="form.name"
                                    required
                                    placeholder="Contoh: Budi Santoso"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.name" />
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Alamat Email (Login) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    required
                                    placeholder="nama@smkn20wm.sch.id"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.email" />
                            </div>

                            <!-- Role -->
                            <div>
                                <label for="role" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Peran / Hak Akses (Role) <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="role"
                                    v-model="form.role"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                    required
                                >
                                    <option value="Staff Picker">Staff Picker (Hanya Transaksi &amp; Picking)</option>
                                    <option value="Admin">Admin (Kelola Barang, Rak &amp; Laporan)</option>
                                    <option value="Warehouse Manager">Warehouse Manager (Akses Penuh Seluruh Sistem)</option>
                                </select>
                                <InputError class="mt-1.5" :message="form.errors.role" />
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Status Akun <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                    required
                                >
                                    <option value="Active">Active (Dapat Login &amp; Bertransaksi)</option>
                                    <option value="Suspended">Suspended (Akun Ditangguhkan)</option>
                                </select>
                                <InputError class="mt-1.5" :message="form.errors.status" />
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Kata Sandi -->
                    <div class="space-y-6 pt-10">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                                <PhKey class="w-5 h-5" weight="duotone" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold tracking-tight text-black">Keamanan &amp; Kata Sandi</h3>
                                <p class="text-xs text-gray-500">Tentukan kata sandi awal untuk pengguna ini.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Kata Sandi Baru <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="password"
                                    type="password"
                                    v-model="form.password"
                                    required
                                    placeholder="Minimal 8 karakter"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.password" />
                            </div>

                            <!-- Password Confirmation -->
                            <div>
                                <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 mb-2">
                                    Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    v-model="form.password_confirmation"
                                    required
                                    placeholder="Ulangi kata sandi"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm"
                                />
                                <InputError class="mt-1.5" :message="form.errors.password_confirmation" />
                            </div>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="flex items-center justify-between pt-8">
                        <Link
                            :href="route('users.index')"
                            class="px-6 py-3 text-sm font-semibold text-gray-600 hover:text-black transition-all rounded-full hover:bg-black/5"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center px-8 py-3.5 bg-terracotta-600 hover:bg-terracotta-700 text-white font-semibold text-sm rounded-full shadow-[0_8px_25px_rgba(193,83,53,0.25)] hover:scale-[1.02] active:scale-95 transition-all duration-300 disabled:opacity-50"
                        >
                            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Pengguna' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
