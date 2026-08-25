<script setup>
import SplitAuthLayout from '@/Layouts/SplitAuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { PhEye, PhEyeSlash } from '@phosphor-icons/vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <SplitAuthLayout title="Daftar Akun WMS" subtitle="Bergabunglah untuk mengelola logistik gudang SMKN 20 Jakarta.">
        <Head title="Register" />

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="name" value="Nama Lengkap" class="text-black font-semibold mb-2" />
                <TextInput
                    id="name"
                    type="text"
                    class="w-full !rounded-xl border-black/10 focus:border-terracotta-500 focus:ring-terracotta-500/20 bg-white shadow-sm px-5 py-3.5 transition-all text-black"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Budi Santoso"
                    :aria-invalid="form.errors.name ? 'true' : null"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email Instansi" class="text-black font-semibold mb-2" />
                <TextInput
                    id="email"
                    type="email"
                    class="w-full !rounded-xl border-black/10 focus:border-terracotta-500 focus:ring-terracotta-500/20 bg-white shadow-sm px-5 py-3.5 transition-all text-black"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="nama@smkn20.sch.id"
                    :aria-invalid="form.errors.email ? 'true' : null"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <InputLabel for="password" value="Kata Sandi" class="text-black font-semibold mb-2" />
                    <div class="relative">
                        <TextInput
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            class="w-full !rounded-xl !pr-11 border-black/10 focus:border-terracotta-500 focus:ring-terracotta-500/20 bg-white shadow-sm px-5 py-3.5 transition-all text-black"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                            :aria-invalid="form.errors.password ? 'true' : null"
                        />
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-terracotta-600 focus:outline-none transition-colors">
                            <PhEye v-if="!showPassword" class="w-5 h-5" aria-hidden="true" />
                            <PhEyeSlash v-else class="w-5 h-5" aria-hidden="true" />
                        </button>
                    </div>
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Konfirmasi Kata Sandi" class="text-black font-semibold mb-2" />
                    <TextInput
                        id="password_confirmation"
                        :type="showPassword ? 'text' : 'password'"
                        class="w-full !rounded-xl border-black/10 focus:border-terracotta-500 focus:ring-terracotta-500/20 bg-white shadow-sm px-5 py-3.5 transition-all text-black"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                        :aria-invalid="form.errors.password_confirmation ? 'true' : null"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div class="pt-4">
                <PrimaryButton
                    class="w-full !rounded-xl justify-center text-base py-3.5 font-bold bg-terracotta-600 hover:bg-terracotta-700 text-white shadow-[0_8px_20px_rgb(211,106,73,0.3)] hover:shadow-[0_8px_25px_rgb(211,106,73,0.4)] hover:-translate-y-0.5 border border-transparent transition-all duration-300"
                    :isLoading="form.processing"
                    :disabled="form.processing"
                >
                    Buat Akun
                </PrimaryButton>
            </div>
        </form>

        <p class="mt-8 text-center text-sm text-gray-600 font-medium">
            Sudah memiliki akun?
            <Link :href="route('login')" class="font-bold text-black hover:text-terracotta-600 hover:underline underline-offset-4 transition-colors">
                Masuk di sini
            </Link>
        </p>
    </SplitAuthLayout>
</template>
