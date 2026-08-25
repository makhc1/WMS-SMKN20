<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { PhArrowLeft } from '@phosphor-icons/vue';

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
            <div class="flex items-center gap-6">
                <Link :href="route('users.index')" class="p-2 -ml-2 rounded-full hover:bg-black/5 text-gray-600 hover:text-black transition-colors">
                    <PhArrowLeft class="w-5 h-5" />
                </Link>
                <h2 class="text-xl font-semibold tracking-tight text-black">
                    Tambah Pengguna Baru
                </h2>
            </div>
        </template>

        <div class="max-w-4xl bg-white border border-black/10 rounded-[1.5rem] overflow-hidden">
            <div class="p-8">
                <form @submit.prevent="submit" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <InputLabel for="name" value="Nama Lengkap" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <!-- Email -->
                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <!-- Password -->
                        <div>
                            <InputLabel for="password" value="Password" />
                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1 block w-full"
                                v-model="form.password"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <InputLabel for="password_confirmation" value="Konfirmasi Password" />
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                v-model="form.password_confirmation"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>

                        <!-- Role -->
                        <div>
                            <InputLabel for="role" value="Role / Jabatan" />
                            <select
                                id="role"
                                v-model="form.role"
                                class="mt-1 block w-full border-gray-300 focus:border-terracotta-500 focus:ring-terracotta-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="Admin">Admin</option>
                                <option value="Warehouse Manager">Warehouse Manager</option>
                                <option value="Staff Picker">Staff Picker</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.role" />
                        </div>

                        <!-- Status -->
                        <div>
                            <InputLabel for="status" value="Status Akses" />
                            <select
                                id="status"
                                v-model="form.status"
                                class="mt-1 block w-full border-gray-300 focus:border-terracotta-500 focus:ring-terracotta-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="Active">Active</option>
                                <option value="Suspended">Suspended (Ditangguhkan)</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-6 pt-6 border-t border-black/5">
                        <Link
                            :href="route('users.index')"
                            class="px-6 py-2.5 text-sm font-semibold text-gray-600 hover:text-black transition-colors rounded-full"
                        >
                            Batal
                        </Link>
                        <PrimaryButton
                            class="!rounded-full px-8 py-3 shadow-[0_8px_20px_rgb(211,106,73,0.25)] hover:shadow-[0_8px_25px_rgb(211,106,73,0.4)] transition-all duration-300"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            Simpan Pengguna
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
