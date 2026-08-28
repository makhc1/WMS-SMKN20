<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { PhWrench, PhClock, PhShieldCheck, PhArrowClockwise, PhUserSwitch, PhWarningCircle, PhCube, PhLockKey, PhRadio, PhCheckCircle } from '@phosphor-icons/vue';
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    details: {
        type: Object,
        default: () => ({
            is_active: true,
            message: 'Sistem sedang dalam proses pemeliharaan berkala untuk peningkatan performa dan sinkronisasi logistik gudang.',
            estimated_finish: null,
            updated_at: null,
            updated_by_name: 'Warehouse Manager',
        }),
    },
    user: {
        type: Object,
        default: null,
    },
});

const isChecking = ref(false);
const isAutoRestoring = ref(false);
let pollInterval = null;

// Manual refresh check button
const checkStatus = () => {
    isChecking.value = true;
    setTimeout(() => {
        router.reload({
            onFinish: () => {
                isChecking.value = false;
            },
        });
    }, 600);
};

// Automatic Real-Time Heartbeat Poller (Checks every 3 seconds)
const pollMaintenanceState = async () => {
    try {
        const res = await fetch(route('system.status'), {
            headers: { 'Accept': 'application/json' }
        });
        if (res.ok) {
            const data = await res.json();
            if (!data.maintenance) {
                // Maintenance was just turned OFF by the Manager!
                isAutoRestoring.value = true;
                if (pollInterval) clearInterval(pollInterval);
                
                // Automatically redirect straight into the application
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 1000);
            }
        }
    } catch (e) {
        // Silently retry on next tick
    }
};

onMounted(() => {
    pollInterval = setInterval(pollMaintenanceState, 3000);
});

onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval);
});
</script>

<template>
    <Head title="Mode Pemeliharaan - WMS SMKN 20" />

    <div class="min-h-[100dvh] bg-[#FAF8F5] text-gray-900 font-sans flex flex-col justify-between p-6 sm:p-12 relative overflow-hidden selection:bg-terracotta-600 selection:text-white">
        
        <!-- Ambient Grain & Radial Glow -->
        <div class="absolute top-0 right-1/4 w-[500px] h-[500px] rounded-full bg-terracotta-600/5 blur-[100px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-1/4 w-[500px] h-[500px] rounded-full bg-amber-500/5 blur-[100px] pointer-events-none"></div>

        <!-- Top Header Navigation -->
        <header class="w-full max-w-4xl mx-auto flex items-center justify-between z-10 animate-[fadeDown_0.8s_cubic-bezier(0.32,0.72,0,1)_forwards]">
            <div class="flex items-center gap-3.5">
                <img src="/logo.jpg" alt="Logo SMKN 20" class="w-11 h-11 object-cover rounded-2xl ring-1 ring-black/10 shadow-sm" />
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-lg tracking-tight text-black leading-none">WMS SMKN 20</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-black/5 text-gray-700 uppercase tracking-wider">Logistik</span>
                    </div>
                    <span class="text-xs text-gray-500 font-medium">Warehouse Management System</span>
                </div>
            </div>

            <div>
                <Link
                    v-if="!user"
                    :href="route('login')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-semibold text-gray-700 bg-white hover:bg-black hover:text-white border border-black/10 transition-all duration-300 shadow-sm hover:scale-105 active:scale-95"
                >
                    <PhUserSwitch class="w-4 h-4" />
                    <span>Login Manajer</span>
                </Link>
                <Link
                    v-else
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200/50 transition-all duration-300 active:scale-95"
                >
                    <span>Keluar ({{ user.name }})</span>
                </Link>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="w-full max-w-3xl mx-auto my-auto py-10 z-10">
            
            <!-- Real-Time Auto-Restore Toast -->
            <transition
                enter-active-class="transition ease-out duration-500"
                enter-from-class="opacity-0 -translate-y-4 scale-95"
                enter-to-class="opacity-100 translate-y-0 scale-100"
            >
                <div v-if="isAutoRestoring" class="mb-6 p-5 rounded-3xl bg-emerald-600 text-white shadow-xl flex items-center justify-between animate-bounce">
                    <div class="flex items-center gap-3.5">
                        <PhCheckCircle class="w-7 h-7 text-white flex-shrink-0" weight="fill" />
                        <div>
                            <h4 class="font-bold text-sm">Pemeliharaan Selesai!</h4>
                            <p class="text-xs text-emerald-100">Sistem telah dibuka kembali. Mengalihkan Anda secara otomatis...</p>
                        </div>
                    </div>
                </div>
            </transition>

            <div class="bg-white rounded-[2.5rem] border border-black/10 shadow-[0_25px_60px_rgba(0,0,0,0.04)] p-8 sm:p-14 relative overflow-hidden animate-[fadeUp_0.9s_cubic-bezier(0.32,0.72,0,1)_forwards]">
                
                <!-- Status Badge with Live Beacon -->
                <div class="flex items-center justify-between gap-4 mb-8">
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-900">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                        </span>
                        <span class="text-xs font-bold uppercase tracking-wider">Pemeliharaan Sedang Berlangsung</span>
                    </div>

                    <div class="w-10 h-10 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 flex-shrink-0">
                        <PhLockKey class="w-5 h-5" weight="duotone" />
                    </div>
                </div>

                <!-- Hero Heading -->
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-black leading-tight mb-5">
                    Sistem Logistik Sedang Dalam <br class="hidden sm:inline" />
                    <span class="text-terracotta-600">Perawatan Terjadwal.</span>
                </h1>

                <!-- Message Description -->
                <p class="text-sm sm:text-base text-gray-600 font-normal leading-relaxed mb-8 max-w-2xl">
                    {{ details.message || 'Mohon maaf atas ketidaknyamanannya. Sistem saat ini sedang dalam proses sinkronisasi database stok fisik gudang dan pemeliharaan performa rutin.' }}
                </p>

                <!-- Information Split Details -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                    <div class="bg-gray-50/80 border border-black/5 rounded-2xl p-5 flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-terracotta-50 flex items-center justify-center flex-shrink-0 text-terracotta-600">
                            <PhClock class="w-5 h-5" weight="duotone" />
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Estimasi Selesai</p>
                            <p class="text-sm font-bold text-black mt-0.5">
                                {{ details.estimated_finish || 'Segera Setelah Selesai' }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-50/80 border border-black/5 rounded-2xl p-5 flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0 text-blue-600">
                            <PhShieldCheck class="w-5 h-5" weight="duotone" />
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Penanggung Jawab</p>
                            <p class="text-sm font-bold text-black mt-0.5">
                                {{ details.updated_by_name || 'Warehouse Manager' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Toolbar & Auto-Polling Indicator -->
                <div class="pt-6 border-t border-black/5 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <button
                        @click="checkStatus"
                        :disabled="isChecking || isAutoRestoring"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 rounded-full bg-black hover:bg-gray-800 text-white text-xs font-semibold transition-all duration-300 shadow-sm hover:scale-[1.02] active:scale-95 disabled:opacity-50"
                    >
                        <PhArrowClockwise :class="['w-4 h-4', { 'animate-spin': isChecking }]" />
                        <span>{{ isChecking ? 'Memeriksa Sistem...' : 'Cek Status Sekarang' }}</span>
                    </button>

                    <div class="text-xs text-gray-500 flex items-center gap-2">
                        <PhRadio class="w-4 h-4 text-emerald-600 animate-pulse" weight="fill" />
                        <span>Halaman ini akan terbuka <strong>otomatis</strong> tanpa perlu refresh manual.</span>
                    </div>
                </div>

            </div>
        </main>

        <!-- Footer Notice -->
        <footer class="w-full max-w-4xl mx-auto text-center z-10 animate-[fadeUp_1.2s_cubic-bezier(0.32,0.72,0,1)_forwards]">
            <p class="text-xs text-gray-400 font-medium">
                &copy; {{ new Date().getFullYear() }} WMS SMKN 20 Jakarta. Sistem Informasi Manajemen Pergudangan.
            </p>
        </footer>

    </div>
</template>

<style>
@keyframes fadeDown {
    0% { opacity: 0; transform: translateY(-1.5rem); filter: blur(3px); }
    100% { opacity: 1; transform: translateY(0); filter: blur(0); }
}
@keyframes fadeUp {
    0% { opacity: 0; transform: translateY(2rem); filter: blur(4px); }
    100% { opacity: 1; transform: translateY(0); filter: blur(0); }
}
</style>
