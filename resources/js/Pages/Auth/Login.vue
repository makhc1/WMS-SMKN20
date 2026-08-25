<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import SplitAuthLayout from '@/Layouts/SplitAuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { PhEye, PhEyeSlash } from '@phosphor-icons/vue';
import { ref } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <SplitAuthLayout title="Masuk ke WMS SMKN 20" subtitle="Masukkan kredensial Anda untuk mengakses sistem manajemen gudang.">
        <Head title="Log in" />

        <div v-if="status" class="mb-6 p-4 rounded-xl text-sm font-semibold text-emerald-700 bg-emerald-100 border border-emerald-200">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Alamat Email" class="text-black font-semibold mb-2" />
                <TextInput
                    id="email"
                    type="email"
                    class="w-full !rounded-xl border-black/10 focus:border-terracotta-500 focus:ring-terracotta-500/20 bg-white shadow-sm px-5 py-3.5 transition-all text-black"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="admin@smkn20.sch.id"
                    :aria-invalid="form.errors.email ? 'true' : null"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <InputLabel for="password" value="Kata Sandi" class="text-black font-semibold" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm font-semibold text-terracotta-600 hover:text-terracotta-800 transition-colors focus:outline-none"
                    >
                        Lupa sandi?
                    </Link>
                </div>
                <div class="relative">
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="w-full !rounded-xl !pr-12 border-black/10 focus:border-terracotta-500 focus:ring-terracotta-500/20 bg-white shadow-sm px-5 py-3.5 transition-all text-black"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        :aria-invalid="form.errors.password ? 'true' : null"
                    />
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-terracotta-600 focus:outline-none transition-colors">
                        <PhEye v-if="!showPassword" class="w-5 h-5" aria-hidden="true" />
                        <PhEyeSlash v-else class="w-5 h-5" aria-hidden="true" />
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center pt-1 pb-4">
                <label class="flex items-center cursor-pointer group">
                    <Checkbox name="remember" v-model:checked="form.remember" class="!rounded border-gray-300 text-terracotta-600 focus:ring-terracotta-600" />
                    <span class="ms-3 text-sm font-medium text-gray-600 group-hover:text-black transition-colors select-none">Ingat perangkat ini</span>
                </label>
            </div>

            <div>
                <PrimaryButton
                    class="w-full !rounded-xl justify-center text-base py-3.5 font-bold bg-terracotta-600 hover:bg-terracotta-700 text-white shadow-[0_8px_20px_rgb(211,106,73,0.3)] hover:shadow-[0_8px_25px_rgb(211,106,73,0.4)] hover:-translate-y-0.5 border border-transparent transition-all duration-300"
                    :isLoading="form.processing"
                    :disabled="form.processing"
                >
                    Masuk ke Sistem
                </PrimaryButton>
            </div>
        </form>

        <p class="mt-8 text-center text-sm text-gray-600 font-medium">
            Belum memiliki akun?
            <Link :href="route('register')" class="font-bold text-black hover:text-terracotta-600 hover:underline underline-offset-4 transition-colors">
                Ajukan akses
            </Link>
        </p>
    </SplitAuthLayout>
</template>
