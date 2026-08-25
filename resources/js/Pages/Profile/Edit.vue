<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profil Pengguna" />

    <AuthenticatedLayout>
        <template #header>
            Profil Pengguna
        </template>

        <div class="grid grid-cols-1 gap-8 max-w-3xl">
            <!-- Update Profile Info -->
            <div class="bg-white border border-black/10 rounded-[1.5rem] overflow-hidden">
                <div class="p-8">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>
            </div>

            <!-- Update Password -->
            <div class="bg-white border border-black/10 rounded-[1.5rem] overflow-hidden">
                <div class="p-8">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>
            </div>

            <!-- Delete User -->
            <div class="bg-red-500/[0.02] border border-red-500/[0.05] p-1.5 rounded-[2rem]">
                <div class="p-8">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
