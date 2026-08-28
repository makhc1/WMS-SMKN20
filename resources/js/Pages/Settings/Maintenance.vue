<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { PhWrench, PhShieldCheck, PhCheckCircle, PhClock, PhFloppyDisk, PhCircleNotch, PhSparkle, PhLockKey, PhEye } from '@phosphor-icons/vue';
import { ref } from 'vue';

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({
            is_active: false,
            message: 'Sistem sedang dalam proses pemeliharaan berkala untuk peningkatan performa.',
            estimated_finish: '',
            updated_at: null,
            updated_by_name: '',
        }),
    },
});

const form = useForm({
    is_active: props.settings.is_active || false,
    message: props.settings.message || 'Sistem sedang dalam proses pemeliharaan berkala untuk peningkatan performa.',
    estimated_finish: props.settings.estimated_finish || '',
});

const isSaved = ref(false);
const toastMessage = ref('');

// Auto-save toggle instantly on click
const handleToggle = () => {
    form.is_active = !form.is_active;
    
    form.post(route('maintenance.update'), {
        preserveScroll: true,
        onSuccess: () => {
            toastMessage.value = form.is_active 
                ? 'Mode Pemeliharaan AKTIF. Akses publik & staf kini terkunci!' 
                : 'Mode Pemeliharaan DINONAKTIFKAN. Sistem kembali beroperasi normal.';
            isSaved.value = true;
            setTimeout(() => {
                isSaved.value = false;
            }, 3500);
        },
    });
};

const saveDetails = () => {
    form.post(route('maintenance.update'), {
        preserveScroll: true,
        onSuccess: () => {
            toastMessage.value = 'Pengumuman pemeliharaan berhasil diperbarui!';
            isSaved.value = true;
            setTimeout(() => {
                isSaved.value = false;
            }, 3500);
        },
    });
};

const presets = [
    {
        title: 'Stock Opname Bulanan',
        message: 'Gudang sedang melakukan penghitungan fisik stok (Stock Opname). Seluruh transaksi Inbound dan Outbound ditutup sementara.',
        finish: 'Pukul 17:00 WIB',
    },
    {
        title: 'Sinkronisasi Database',
        message: 'Sistem sedang dalam proses sinkronisasi database berkala dan backup data inventaris.',
        finish: '1 Jam ke depan',
    },
    {
        title: 'Audit & Penataan Rak',
        message: 'Sedang dilakukan penataan kembali lokasi rak fisik dan pembaruan kode slot gudang.',
        finish: '2 Jam ke depan',
    },
];

const applyPreset = (preset) => {
    form.message = preset.message;
    form.estimated_finish = preset.finish;
};
</script>

<template>
    <Head title="Pengaturan Mode Pemeliharaan" />

    <AuthenticatedLayout>
        <template #header>
            <div class="max-w-4xl mx-auto">
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-black leading-tight">
                    Mode Pemeliharaan (Maintenance)
                </h2>
                <p class="text-xs text-gray-500 font-medium">Panel kontrol manajer untuk mengunci dan membuka akses sistem.</p>
            </div>
        </template>

        <div class="max-w-4xl mx-auto pb-16 space-y-8">

            <!-- Toast Success Notification -->
            <transition
                enter-active-class="transition ease-out duration-300"
                enter-from-class="opacity-0 -translate-y-2 scale-95"
                enter-to-class="opacity-100 translate-y-0 scale-100"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100 translate-y-0 scale-100"
                leave-to-class="opacity-0 -translate-y-2 scale-95"
            >
                <div v-if="isSaved" class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-800 shadow-sm">
                    <div class="flex items-center gap-3">
                        <PhCheckCircle class="w-5 h-5 text-emerald-600 flex-shrink-0" weight="fill" />
                        <span class="text-sm font-semibold">{{ toastMessage }}</span>
                    </div>
                </div>
            </transition>

            <!-- Master Status Control Card with Smooth Standard Switcher -->
            <div 
                :class="[
                    'p-8 sm:p-10 rounded-3xl border transition-all duration-500 relative overflow-hidden',
                    form.is_active 
                        ? 'bg-amber-500/10 border-amber-500/40 shadow-[0_20px_50px_rgba(245,158,11,0.1)]' 
                        : 'bg-white border-black/10 shadow-sm'
                ]"
            >
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                    <div class="flex items-start gap-4">
                        <div 
                            :class="[
                                'w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 transition-colors duration-500',
                                form.is_active ? 'bg-amber-500 text-white shadow-md shadow-amber-500/30' : 'bg-gray-100 text-gray-700'
                            ]"
                        >
                            <PhLockKey v-if="form.is_active" class="w-6 h-6" weight="duotone" />
                            <PhWrench v-else class="w-6 h-6" weight="duotone" />
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="text-xl font-bold tracking-tight text-black">
                                    {{ form.is_active ? 'Mode Pemeliharaan Aktif' : 'Sistem Berjalan Normal' }}
                                </h3>
                                <span 
                                    :class="[
                                        'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                                        form.is_active ? 'bg-amber-100 text-amber-800' : 'bg-emerald-50 text-emerald-700'
                                    ]"
                                >
                                    {{ form.is_active ? 'Sistem Terkunci' : 'Publik Aktif' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 mt-1.5 max-w-lg leading-relaxed">
                                {{ form.is_active 
                                    ? 'Akses untuk staf dan publik dialihkan ke halaman pemberitahuan. Hanya Warehouse Manager yang dapat mengakses sistem.'
                                    : 'Seluruh staf dan admin dapat login dan menjalankan transaksi logistik secara normal.' 
                                }}
                            </p>
                        </div>
                    </div>

                    <!-- Smooth Standard iOS/Tailwind Switcher -->
                    <div class="flex items-center gap-3.5 self-start sm:self-auto bg-black/[0.02] sm:bg-transparent p-2 sm:p-0 rounded-2xl">
                        <span class="text-xs font-semibold text-gray-500 select-none">
                            {{ form.is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>

                        <button
                            type="button"
                            role="switch"
                            :aria-checked="form.is_active"
                            @click="handleToggle"
                            :disabled="form.processing"
                            :class="[
                                'relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:ring-offset-2 disabled:opacity-50 shadow-inner',
                                form.is_active ? 'bg-amber-500' : 'bg-gray-200'
                            ]"
                            title="Klik untuk mengaktifkan / menonaktifkan"
                        >
                            <span class="sr-only">Toggle Mode Pemeliharaan</span>
                            <span
                                :class="[
                                    'pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-md ring-0 transition duration-300 ease-in-out flex items-center justify-center',
                                    form.is_active ? 'translate-x-5' : 'translate-x-0'
                                ]"
                            >
                                <PhCircleNotch v-if="form.processing" class="w-3 h-3 text-amber-500 animate-spin" />
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Last Update Metadata -->
                <div v-if="settings.updated_at" class="mt-6 pt-6 border-t border-black/5 flex flex-wrap items-center gap-6 text-xs text-gray-500">
                    <span class="flex items-center gap-1.5">
                        <PhClock class="w-4 h-4 text-gray-400" />
                        Terakhir diubah: <strong class="text-black">{{ settings.updated_at }}</strong>
                    </span>
                    <span v-if="settings.updated_by_name" class="flex items-center gap-1.5">
                        <PhShieldCheck class="w-4 h-4 text-gray-400" />
                        Oleh: <strong class="text-black">{{ settings.updated_by_name }}</strong>
                    </span>
                </div>
            </div>

            <!-- Configuration Section & Preset Buttons -->
            <div class="bg-white rounded-3xl border border-black/10 shadow-sm p-8 sm:p-10 space-y-8">
                <div>
                    <h4 class="text-lg font-bold tracking-tight text-black">Detail Pesan Pengumuman</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Informasikan alasan pemeliharaan kepada staf yang membuka sistem.</p>
                </div>

                <!-- Quick Presets (1-Click Fillers) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2.5">
                        Template Cepat (Pilih Alasan Pemeliharaan):
                    </label>
                    <div class="flex flex-wrap gap-2.5">
                        <button
                            type="button"
                            v-for="preset in presets"
                            :key="preset.title"
                            @click="applyPreset(preset)"
                            class="px-4 py-2 rounded-xl bg-gray-50 hover:bg-terracotta-50 hover:text-terracotta-700 border border-black/5 text-xs font-semibold text-gray-700 transition-all active:scale-95 flex items-center gap-1.5 shadow-sm"
                        >
                            <PhSparkle class="w-3.5 h-3.5 text-terracotta-600" />
                            <span>{{ preset.title }}</span>
                        </button>
                    </div>
                </div>

                <!-- Message Form -->
                <form @submit.prevent="saveDetails" class="space-y-6 pt-4 border-t border-black/5">
                    <!-- Message Textarea -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                            Pesan Pemberitahuan untuk Pengguna
                        </label>
                        <textarea
                            v-model="form.message"
                            rows="3"
                            class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm text-gray-900 placeholder-gray-400"
                            placeholder="Tuliskan alasan pemeliharaan sistem..."
                            required
                        ></textarea>
                    </div>

                    <!-- Estimated Finish Time -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                            Estimasi Waktu Selesai (Opsional)
                        </label>
                        <input
                            v-model="form.estimated_finish"
                            type="text"
                            class="w-full px-4 py-3 rounded-xl bg-white border border-black/10 focus:border-black focus:ring-1 focus:ring-black transition-all text-sm text-gray-900 placeholder-gray-400"
                            placeholder="Contoh: Pukul 18:00 WIB / 30 Menit ke depan"
                        />
                    </div>

                    <!-- Save Details Button -->
                    <div class="flex items-center justify-end pt-4 border-t border-black/5">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 px-7 py-3 rounded-full bg-black hover:bg-gray-800 text-white font-semibold text-xs transition-all shadow-sm hover:scale-105 active:scale-95 disabled:opacity-50"
                        >
                            <PhFloppyDisk class="w-4 h-4" />
                            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Pesan Pengumuman' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Live Screen Simulation Preview -->
            <div class="bg-gray-50 rounded-3xl border border-black/5 p-6 sm:p-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <PhEye class="w-4 h-4 text-gray-500" />
                        <h5 class="text-xs font-semibold text-gray-700 uppercase tracking-wider">Pratinjau Langsung (Live Screen Preview)</h5>
                    </div>
                    <span class="text-[11px] text-gray-400">Tampilan saat staf membuka web saat MT aktif</span>
                </div>
                
                <div class="bg-white rounded-2xl border border-black/10 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider">Pemeliharaan Terjadwal</span>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-400">WMS SMKN 20</span>
                    </div>
                    <h4 class="text-base font-bold tracking-tight text-black mb-1">Sistem Sedang Dalam Perawatan</h4>
                    <p class="text-xs text-gray-600 mb-4 leading-relaxed">{{ form.message || 'Pesan belum diisi...' }}</p>
                    <div class="text-xs text-gray-500 flex items-center justify-between pt-3 border-t border-black/5">
                        <span class="flex items-center gap-1.5">
                            <PhClock class="w-3.5 h-3.5 text-terracotta-600" />
                            Estimasi: <strong class="text-black">{{ form.estimated_finish || 'Segera' }}</strong>
                        </span>
                        <span class="text-[10px] text-gray-400">Status 503 Service Unavailable</span>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
