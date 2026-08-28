<script setup>
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { PhArrowLeft, PhPrinter, PhCheck, PhPackage } from '@phosphor-icons/vue';

const props = defineProps({
    pickingList: Object,
});

const markItemPicked = (itemId) => {
    router.post(route('picking-lists.pick', props.pickingList.id), {
        item_id: itemId
    });
};

const completePickingList = () => {
    if (confirm('Yakin ingin menyelesaikan Picking List ini?')) {
        router.post(route('picking-lists.complete', props.pickingList.id));
    }
};

const printPickingList = () => {
    window.print();
};

const getStatusBadge = (status) => {
    switch (status) {
        case 'completed':
            return 'bg-emerald-50 text-emerald-600';
        case 'pending':
            return 'bg-amber-50 text-amber-600';
        default:
            return 'bg-gray-50 text-gray-600';
    }
};

const getItemStatusBadge = (status) => {
    switch (status) {
        case 'picked':
            return 'bg-emerald-50 text-emerald-600';
        case 'pending':
            return 'bg-amber-50 text-amber-600';
        default:
            return 'bg-gray-50 text-gray-600';
    }
};
</script>

<template>
    <Head :title="`Picking List - ${pickingList.code}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4 print:hidden">
                <Link :href="route('picking-lists.index')" class="text-gray-600 hover:text-gray-700 transition-colors">
                    <PhArrowLeft class="w-6 h-6" />
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 tracking-tight">
                    Picking List {{ pickingList.code }}
                </h2>
            </div>
        </template>

        <div class="max-w-4xl">
            <!-- Header Info -->
            <div class="bg-white border border-black/10 rounded-[1.5rem] p-6 md:p-8 mb-6 print:border-0 print:rounded-none">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-bold tracking-tighter text-black">{{ pickingList.code }}</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Dibuat: {{ new Date(pickingList.created_at).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
                        </p>
                        <p v-if="pickingList.notes" class="text-sm text-gray-600 mt-1">
                            Catatan: {{ pickingList.notes }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span :class="[
                            'inline-flex items-center rounded-full px-4 py-1.5 text-xs font-bold tracking-tight',
                            getStatusBadge(pickingList.status)
                        ]">
                            {{ pickingList.status === 'completed' ? 'Selesai' : 'Pending' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Items List -->
            <div class="bg-white border border-black/10 rounded-[1.5rem] overflow-hidden print:border-0 print:rounded-none">
                <div class="p-6 md:p-8 border-b border-black/5 print:p-4">
                    <h4 class="text-sm font-bold tracking-tight text-black">Daftar Barang</h4>
                </div>
                
                <div class="divide-y divide-black/5">
                    <div v-for="item in pickingList.items" :key="item.id" class="p-6 md:p-8 flex items-center justify-between print:p-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center overflow-hidden border border-black/5 print:w-10 print:h-10">
                                <img v-if="item.photo" :src="'/storage/' + item.photo" alt="Photo" class="w-full h-full object-cover" />
                                <PhPackage v-else class="w-6 h-6 text-gray-400" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-black">{{ item.name }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ item.sku }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Rak: <span class="font-mono font-semibold">{{ item.pivot.location_id }}</span>
                                    <span class="mx-1">•</span>
                                    Jumlah: <span class="font-semibold">{{ item.pivot.quantity }} {{ item.unit || 'Pcs' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span :class="[
                                'inline-flex items-center rounded-full px-3 py-1 text-xs font-bold tracking-tight',
                                getItemStatusBadge(item.pivot.status)
                            ]">
                                {{ item.pivot.status === 'picked' ? 'Diambil' : 'Pending' }}
                            </span>
                            <button
                                v-if="pickingList.status === 'pending' && item.pivot.status === 'pending'"
                                @click="markItemPicked(item.id)"
                                class="p-2 text-gray-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-full transition-colors duration-300 print:hidden"
                                title="Tandai sudah diambil"
                            >
                                <PhCheck class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="p-6 md:p-8 bg-gray-50 border-t border-black/5 print:p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total: {{ pickingList.items.length }} barang</span>
                        <span class="text-sm font-semibold text-black">
                            {{ pickingList.items.filter(i => i.pivot.status === 'picked').length }} / {{ pickingList.items.length }} diambil
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 mt-6 print:hidden">
                <button
                    @click="printPickingList"
                    class="inline-flex items-center gap-2 px-6 py-3 border border-black/10 rounded-full text-sm font-semibold text-gray-600 hover:text-black hover:bg-black/5 transition-colors"
                >
                    <PhPrinter class="w-4 h-4" />
                    Cetak
                </button>
                <button
                    v-if="pickingList.status === 'pending'"
                    @click="completePickingList"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white rounded-full text-sm font-semibold hover:bg-emerald-700 transition-colors"
                >
                    <PhCheck class="w-4 h-4" />
                    Selesaikan
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@media print {
    body {
        background-color: white !important;
    }
    
    header, nav, aside, .print\:hidden {
        display: none !important;
    }

    main {
        margin: 0 !important;
        padding: 0 !important;
    }
}
</style>
