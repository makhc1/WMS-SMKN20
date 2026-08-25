<script setup>
import SplitAuthLayout from '@/Layouts/SplitAuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <SplitAuthLayout title="Reset password" subtitle="Enter your email to receive a password reset link.">
        <Head title="Forgot Password" />

        <div
            v-if="status"
            class="mb-6 p-6 sm:p-8 rounded-xl text-sm font-medium text-green-700 bg-green-50 border border-green-100"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Email Address" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-2"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="john@example.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full"
                    :isLoading="form.processing"
                    :disabled="form.processing"
                >
                    Email Reset Link
                </PrimaryButton>
            </div>
            
            <div class="text-center mt-6">
                <p class="text-sm text-zinc-500">
                    Remember your password? 
                    <Link :href="route('login')" class="font-medium text-zinc-900 hover:text-terracotta-600 transition-colors">
                        Back to login
                    </Link>
                </p>
            </div>
        </form>
    </SplitAuthLayout>
</template>
