<script setup>
import { computed } from 'vue';
import SplitAuthLayout from '@/Layouts/SplitAuthLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <SplitAuthLayout title="Verify your email" subtitle="Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?">
        <Head title="Email Verification" />

        <div
            class="mb-6 p-6 sm:p-8 rounded-xl text-sm font-medium text-green-700 bg-green-50 border border-green-100"
            v-if="verificationLinkSent"
        >
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="pt-2">
                <PrimaryButton
                    class="w-full"
                    :isLoading="form.processing"
                    :disabled="form.processing"
                >
                    Resend Verification Email
                </PrimaryButton>
            </div>
            
            <div class="text-center mt-6">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-sm font-medium text-zinc-500 hover:text-terracotta-600 transition-colors focus:outline-none focus:ring-2 focus:ring-terracotta-500 rounded-md px-1"
                >
                    Log Out
                </Link>
            </div>
        </form>
    </SplitAuthLayout>
</template>
