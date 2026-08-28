<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { PhHouse, PhCube, PhArrowDownLeft, PhArrowUpRight, PhSignOut, PhList, PhX, PhBell, PhWarningCircle, PhFileText, PhUser, PhMapPin, PhClock, PhWrench } from '@phosphor-icons/vue';

const page = usePage();
const userRole = page.props.auth.user.role;

const navigation = [
    { name: 'Dashboard', route: 'dashboard', icon: PhHouse },
    { name: 'Master Barang', route: 'items.index', icon: PhCube },
    { name: 'Inbound', route: 'inbound.index', icon: PhArrowDownLeft },
    { name: 'Outbound', route: 'outbound.index', icon: PhArrowUpRight },
    { name: 'Riwayat', route: 'riwayat.index', icon: PhClock },
    // Only Admin and Warehouse Manager can access these
    ...( ['Admin', 'Warehouse Manager'].includes(userRole) ? [
        { name: 'Lokasi Rak', route: 'locations.index', icon: PhMapPin },
        { name: 'Laporan', route: 'reports.index', icon: PhFileText },
    ] : []),
    // Only Warehouse Manager can manage users & maintenance
    ...( userRole === 'Warehouse Manager' ? [
        { name: 'Manajemen Pengguna', route: 'users.index', icon: PhUser },
        { name: 'Mode Pemeliharaan', route: 'maintenance.index', icon: PhWrench },
    ] : [])
];

const mobileMenuOpen = ref(false);
const showNotifications = ref(false);

// Auto-detect when Maintenance Mode starts in real-time
let maintenanceHeartbeat = null;

onMounted(() => {
    if (userRole !== 'Warehouse Manager') {
        maintenanceHeartbeat = setInterval(async () => {
            try {
                const res = await fetch(route('system.status'), {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data.maintenance) {
                        if (maintenanceHeartbeat) clearInterval(maintenanceHeartbeat);
                        window.location.reload();
                    }
                }
            } catch (e) {}
        }, 5000);
    }
});

onUnmounted(() => {
    if (maintenanceHeartbeat) clearInterval(maintenanceHeartbeat);
});
</script>

<template>
    <div class="min-h-[100dvh] bg-terracotta-50 selection:bg-terracotta-600 selection:text-white font-sans text-gray-900 flex">
        
        <!-- Mobile Header (Visible only on md-) -->
        <header class="md:hidden fixed top-0 inset-x-0 h-20 bg-white/80 backdrop-blur-xl border-b border-black/5 z-40 flex items-center justify-between px-6">
            <div class="font-bold text-lg tracking-tighter text-black flex items-center gap-3">
                <img src="/logo.jpg" alt="Logo" class="w-8 h-8 object-cover rounded-full ring-2 ring-terracotta-600/20" />
                WMS
            </div>
            <button @click="mobileMenuOpen = true" class="w-10 h-10 rounded-full bg-black/5 flex items-center justify-center transition-transform active:scale-95">
                <PhList class="w-5 h-5 text-black" />
            </button>
        </header>

        <!-- Floating Sidebar (Desktop) -->
        <aside class="hidden md:flex fixed top-6 left-6 bottom-6 w-[280px] bg-white rounded-[2.5rem] border border-black/5 shadow-[0_20px_40px_rgb(0,0,0,0.06)] z-50">
            <div class="w-full h-full flex flex-col overflow-hidden relative">
                
                <!-- Logo -->
                <div class="px-8 pt-10 pb-8 flex items-center gap-3">
                    <img src="/logo.jpg" alt="Logo WMS SMKN 20" class="w-12 h-12 object-cover rounded-full shadow-sm ring-2 ring-terracotta-600/20" />
                    <span class="font-bold text-2xl tracking-tighter text-black">WMS</span>
                </div>

                <!-- Nav -->
                <nav class="flex-1 px-6 lg:px-8 space-y-2 overflow-y-auto">
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="route(item.route)"
                        :class="[
                            route().current(item.route) 
                                ? 'bg-terracotta-600 text-white shadow-[0_8px_20px_rgb(193,83,53,0.25)]' 
                                : 'text-gray-600 hover:bg-terracotta-50 hover:text-terracotta-900 hover:translate-x-1',
                            'group flex items-center px-5 py-6 text-sm font-semibold rounded-[1.5rem] transition-all duration-700 ease-[cubic-bezier(0.32,0.72,0,1)]'
                        ]"
                    >
                        <component 
                            :is="item.icon" 
                            :class="[
                                route().current(item.route) ? 'text-white' : 'text-gray-600 group-hover:text-terracotta-600',
                                'flex-shrink-0 mr-4 h-5 w-5 transition-colors duration-700 ease-[cubic-bezier(0.32,0.72,0,1)]'
                            ]" 
                            weight="duotone"
                        />
                        {{ item.name }}
                    </Link>
                </nav>

                <!-- User Area -->
                <div class="p-6 sm:p-8 mt-auto">
                    <div class="bg-black/5 rounded-[1.5rem] p-2 flex items-center justify-between gap-3">
                        <Link :href="route('profile.edit')" class="flex-1 flex items-center gap-3 overflow-hidden group/profile hover:bg-white/80 p-2 rounded-[1.25rem] transition-all duration-500">
                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center flex-shrink-0 shadow-[0_4px_10px_rgb(0,0,0,0.05)] font-bold text-sm group-hover/profile:scale-105 transition-transform duration-500">
                                {{ $page.props.auth.user.name.charAt(0) }}
                            </div>
                            <span class="text-sm font-semibold text-black truncate">{{ $page.props.auth.user.name }}</span>
                        </Link>
                        <Link :href="route('logout')" method="post" as="button" class="flex items-center justify-center w-10 h-10 rounded-full bg-white hover:bg-red-50 hover:text-red-600 transition-all duration-500 shadow-[0_4px_10px_rgb(0,0,0,0.05)] flex-shrink-0">
                            <PhSignOut class="w-4 h-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Mobile Menu Modal (Full Screen) -->
        <div 
            v-if="mobileMenuOpen" 
            class="fixed inset-0 z-[100] bg-white/95 backdrop-blur-3xl flex flex-col px-6 py-24 transition-all duration-700 ease-[cubic-bezier(0.32,0.72,0,1)]"
        >
            <button @click="mobileMenuOpen = false" class="absolute top-6 right-6 w-12 h-12 flex items-center justify-center rounded-full bg-black/5 hover:bg-black/10 transition-colors">
                <PhX class="w-6 h-6 text-black" />
            </button>
            
            <div class="flex flex-col gap-6 mt-12">
                <Link
                    v-for="(item, index) in navigation"
                    :key="item.name"
                    :href="route(item.route)"
                    class="text-4xl tracking-tighter leading-none font-bold tracking-tighter text-black opacity-0 animate-[slideUp_0.7s_cubic-bezier(0.32,0.72,0,1)_forwards]"
                    :style="`animation-delay: ${index * 100}ms`"
                >
                    {{ item.name }}
                </Link>
                <div class="mt-8 pt-8 border-t border-black/10 opacity-0 animate-[slideUp_0.7s_cubic-bezier(0.32,0.72,0,1)_forwards]" style="animation-delay: 400ms">
                    <Link :href="route('logout')" method="post" as="button" class="text-xl font-medium text-gray-600 hover:text-black">
                        Sign Out ({{ $page.props.auth.user.name }})
                    </Link>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        
        <!-- Top Right Floating Elements -->
        <div class="fixed top-6 right-6 md:right-12 z-[60] flex items-center gap-6">
            <div class="relative">
                <button @click="showNotifications = !showNotifications" class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-black/5 hover:scale-105 transition-transform duration-500 relative">
                    <PhBell class="w-6 h-6 text-black" />
                    <!-- Badge -->
                    <div v-if="$page.props.lowStockItems && $page.props.lowStockItems.length > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-terracotta-600 rounded-full flex items-center justify-center text-[10px] font-bold text-white shadow-sm ring-2 ring-white">
                        {{ $page.props.lowStockItems.length }}
                    </div>
                </button>

                <!-- Dropdown / Popup -->
                <transition
                    enter-active-class="transition ease-[cubic-bezier(0.32,0.72,0,1)] duration-500"
                    enter-from-class="opacity-0 translate-y-4 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition ease-in duration-300"
                    leave-from-class="opacity-100 translate-y-0 scale-100"
                    leave-to-class="opacity-0 translate-y-4 scale-95"
                >
                    <div v-if="showNotifications" class="absolute top-16 right-0 w-80 md:w-96 bg-white rounded-[2rem] shadow-[0_20px_60px_rgb(0,0,0,0.12)] border border-black/5 overflow-hidden flex flex-col">
                        <div class="p-6 border-b border-black/5 flex items-center justify-between bg-black/[0.01]">
                            <h3 class="font-bold tracking-tighter text-black text-lg">Notifikasi</h3>
                            <span v-if="$page.props.lowStockItems && $page.props.lowStockItems.length > 0" class="text-xs font-semibold text-terracotta-600 bg-terracotta-50 px-4 sm:px-6 py-1 rounded-full">{{ $page.props.lowStockItems.length }} Peringatan</span>
                        </div>
                        <div class="max-h-[60vh] overflow-y-auto p-2">
                            <template v-if="$page.props.lowStockItems && $page.props.lowStockItems.length > 0">
                                <Link 
                                    v-for="item in $page.props.lowStockItems" 
                                    :key="item.id" 
                                    :href="route('items.edit', item.id)"
                                    class="p-6 sm:p-8 m-2 rounded-2xl hover:bg-black/[0.02] transition-colors border border-transparent hover:border-black/5 group flex items-start gap-6"
                                >
                                    <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-500">
                                        <PhWarningCircle class="w-5 h-5 text-red-600" />
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-black tracking-tight text-sm leading-tight">{{ item.name }}</h4>
                                        <p class="text-xs text-gray-600 mt-1 mb-2 font-mono">{{ item.sku }}</p>
                                        <div class="flex items-center gap-3 text-xs">
                                            <span class="font-semibold text-red-600 bg-red-50 px-4 sm:px-6 py-0.5 rounded-full">Sisa: {{ item.quantity }}</span>
                                            <span class="text-gray-600 font-medium">Batas: {{ item.low_stock_threshold }}</span>
                                        </div>
                                    </div>
                                </Link>
                            </template>
                            <template v-else>
                                <div class="p-8 text-center flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center mb-3">
                                        <PhCube class="w-6 h-6 text-green-500" />
                                    </div>
                                    <p class="font-semibold text-black">Semua Aman</p>
                                    <p class="text-xs text-gray-600 mt-1">Tidak ada peringatan stok saat ini.</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </transition>
            </div>
        </div>

        <!-- Margin left accounts for sidebar width (280px + 24px left + 24px right = 328px ~ 20.5rem) -->
        <main class="flex-1 md:ml-[328px] flex flex-col min-h-[100dvh] pt-28 md:pt-16 px-6 md:pr-12 md:pl-0 pb-32">
            
            <div class="w-full max-w-[1400px] mx-auto">
                <!-- Maintenance Active Alert Banner (Visible when maintenance mode is ON) -->
                <div 
                    v-if="$page.props.systemMaintenance && $page.props.systemMaintenance.is_active" 
                    class="mb-8 p-4 sm:p-5 rounded-2xl bg-amber-500 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg shadow-amber-500/20 animate-[fadeDown_0.6s_cubic-bezier(0.32,0.72,0,1)_forwards]"
                >
                    <div class="flex items-center gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
                            <PhWrench class="w-5 h-5 text-white" weight="bold" />
                        </div>
                        <div>
                            <p class="text-sm font-bold tracking-tight">Mode Pemeliharaan Sedang Aktif</p>
                            <p class="text-xs text-white/80">Sistem terkunci untuk staf dan publik. Hanya Anda (Warehouse Manager) yang memiliki akses.</p>
                        </div>
                    </div>
                    <Link 
                        :href="route('maintenance.index')" 
                        class="self-start sm:self-auto text-xs font-bold bg-white text-black px-5 py-2 rounded-full hover:bg-gray-100 transition-all hover:scale-105 active:scale-95 shadow-sm whitespace-nowrap"
                    >
                        Kelola Status
                    </Link>
                </div>

                <!-- Header Area -->
                <div class="mb-8 md:mb-12 animate-[fadeUp_1s_cubic-bezier(0.32,0.72,0,1)_forwards]">
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight text-black leading-tight">
                        <slot name="header" />
                    </h1>
                </div>

                <!-- Page Content -->
                <div class="flex-1 w-full animate-[fadeUp_1.2s_cubic-bezier(0.32,0.72,0,1)_forwards]">
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>

<style>
@keyframes fadeUp {
    0% { opacity: 0; transform: translateY(2rem); filter: blur(4px); }
    100% { opacity: 1; transform: translateY(0); filter: blur(0); }
}
@keyframes slideUp {
    0% { opacity: 0; transform: translateY(2rem); }
    100% { opacity: 1; transform: translateY(0); }
}
</style>
