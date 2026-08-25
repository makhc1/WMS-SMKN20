<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { PhArrowLeft } from '@phosphor-icons/vue';

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
    <Head title="Edit Lokasi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-6">
                <Link :href="route('locations.index')" class="p-2 -ml-2 rounded-full hover:bg-black/5 text-gray-600 hover:text-black transition-colors">
                    <PhArrowLeft class="w-5 h-5" />
                </Link>
                <h2 class="text-xl font-semibold tracking-tight text-black">
                    Edit Lokasi/Rak: {{ location.code }}
                </h2>
            </div>
        </template>

        <div class="max-w-4xl bg-white border border-black/10 rounded-[1.5rem] overflow-hidden">
            <div class="p-8">
                <form @submit.prevent="submit" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Code -->
                        <div>
                            <InputLabel for="code" value="Kode Rak / Area" />
                            <TextInput
                                id="code"
                                type="text"
                                class="mt-1 block w-full bg-gray-50 cursor-not-allowed"
                                v-model="form.code"
                                readonly
                            />
                            <InputError class="mt-2" :message="form.errors.code" />
                        </div>

                        <!-- Name -->
                        <div>
                            <InputLabel for="name" value="Nama Rak" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <!-- Zone -->
                        <div>
                            <InputLabel for="zone_name" value="Zona (Opsional)" />
                            <TextInput
                                id="zone_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.zone_name"
                            />
                            <InputError class="mt-2" :message="form.errors.zone_name" />
                        </div>

                        <!-- Storage Type -->
                        <div>
                            <InputLabel for="storage_type" value="Tipe Penyimpanan" />
                            <select
                                id="storage_type"
                                v-model="form.storage_type"
                                class="mt-1 block w-full border-gray-300 focus:border-terracotta-500 focus:ring-terracotta-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="Dry">Dry (Kering)</option>
                                <option value="Cold Storage">Cold Storage (Pendingin)</option>
                                <option value="Hazardous">Hazardous (Berbahaya)</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.storage_type" />
                        </div>

                        <!-- Capacity -->
                        <div>
                            <InputLabel for="capacity_percentage" value="Kapasitas Terpakai (%)" />
                            <div class="flex items-center gap-6 mt-1">
                                <input
                                    id="capacity_percentage"
                                    type="range"
                                    min="0"
                                    max="100"
                                    v-model="form.capacity_percentage"
                                    class="flex-1 accent-terracotta-600"
                                />
                                <span class="text-sm font-semibold text-black w-12">{{ form.capacity_percentage }}%</span>
                            </div>
                            <InputError class="mt-2" :message="form.errors.capacity_percentage" />
                        </div>

                        <!-- Status -->
                        <div>
                            <InputLabel for="status" value="Status" />
                            <select
                                id="status"
                                v-model="form.status"
                                class="mt-1 block w-full border-gray-300 focus:border-terracotta-500 focus:ring-terracotta-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="Active">Active</option>
                                <option value="Under Maintenance">Under Maintenance</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-6 pt-6 border-t border-black/5">
                        <Link
                            :href="route('locations.index')"
                            class="px-6 py-2.5 text-sm font-semibold text-gray-600 hover:text-black transition-colors rounded-full"
                        >
                            Batal
                        </Link>
                        <PrimaryButton
                            class="!rounded-full px-8 py-3 shadow-[0_8px_20px_rgb(211,106,73,0.25)] hover:shadow-[0_8px_25px_rgb(211,106,73,0.4)] transition-all duration-300"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            Simpan Perubahan
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
