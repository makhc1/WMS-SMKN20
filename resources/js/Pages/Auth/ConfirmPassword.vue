<script setup>
import SplitAuthLayout from '@/Layouts/SplitAuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <SplitAuthLayout title="Security Confirmation" subtitle="This is a secure area. Please confirm your password before continuing.">
        <Head title="Confirm Password" />

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="password" value="Confirm Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-2"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    autofocus
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full"
                    :isLoading="form.processing"
                    :disabled="form.processing"
                >
                    Confirm Access
                </PrimaryButton>
            </div>
        </form>
    </SplitAuthLayout>
</template>
