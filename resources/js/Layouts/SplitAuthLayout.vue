<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

defineProps({
    title: {
        type: String,
        default: 'Selamat Datang'
    },
    subtitle: {
        type: String,
        default: 'Silakan masukkan detail Anda untuk melanjutkan.'
    }
});

const backgroundImages = [
    '/wms_login_bg.jpg',
    '/wms_login_bg_2.jpg',
    '/wms_login_bg_3.jpg'
];

const currentImageIndex = ref(0);
let intervalId = null;

onMounted(() => {
    intervalId = setInterval(() => {
        currentImageIndex.value = (currentImageIndex.value + 1) % backgroundImages.length;
    }, 5000);
});

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId);
});
</script>

<template>
    <div class="min-h-[100dvh] flex flex-col lg:grid lg:grid-cols-2 bg-terracotta-50 font-sans selection:bg-terracotta-600 selection:text-white">
        <!-- Left Side: Form Container -->
        <div class="flex flex-col justify-center px-8 py-12 sm:px-16 md:px-24 xl:px-32 z-10 relative bg-terracotta-50 h-full">
            <div class="absolute top-8 left-8 sm:top-12 sm:left-12 lg:left-16">
                <Link href="/" class="flex items-center gap-4 group">
                    <img src="/logo.jpg" alt="Logo SMKN 20" class="w-16 h-16 object-cover rounded-full shadow-md ring-4 ring-terracotta-600/20 transition-transform duration-500 group-hover:scale-105" />
                    <span class="font-bold text-2xl tracking-tighter text-black">WMS <span class="font-light text-terracotta-700">SMKN 20</span></span>
                </Link>
            </div>
            
            <div class="w-full max-w-md mx-auto mt-24 lg:mt-0 animate-[fadeUp_0.8s_cubic-bezier(0.32,0.72,0,1)_forwards]">
                <h1 class="text-3xl lg:text-4xl font-semibold tracking-tighter text-black mb-3 leading-tight">{{ title }}</h1>
                <p class="text-gray-600 mb-8 leading-relaxed">{{ subtitle }}</p>
                
                <slot />
            </div>
        </div>

        <!-- Right Side: Brand / Premium Visual -->
        <div class="hidden lg:flex relative bg-[#0a0a0a] overflow-hidden flex-col justify-between p-16 shadow-[-20px_0_40px_rgb(0,0,0,0.1)] rounded-l-[3rem] my-4 mr-4">
            
            <!-- Slideshow Backgrounds -->
            <transition-group name="fade" tag="div">
                <img 
                    v-for="(img, index) in backgroundImages" 
                    :key="img"
                    v-show="currentImageIndex === index"
                    :src="img" 
                    alt="WMS SMKN 20 Logistics" 
                    class="absolute inset-0 w-full h-full object-cover mix-blend-luminosity fade-img"
                />
            </transition-group>

            <!-- Overlay Gradient -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] via-[#0a0a0a]/60 to-transparent pointer-events-none"></div>
            
            <!-- Glow effect -->
            <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-terracotta-500/20 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>

            <div class="relative z-10 max-w-xl mt-auto">
                <h2 class="text-4xl xl:text-5xl font-medium text-white tracking-tighter leading-[1.1] mb-6 drop-shadow-lg">
                    Logistik <span class="text-terracotta-400">terpadu.</span><br/>
                    Presisi <span class="text-terracotta-400">SMKN 20.</span>
                </h2>
                <p class="text-lg text-gray-300 max-w-md leading-relaxed drop-shadow">
                    Infrastruktur manajemen gudang modern untuk SMKN 20 Jakarta. Lacak barang masuk, optimalkan pengeluaran, dan jaga akurasi inventaris secara absolut.
                </p>

                <div class="mt-12 flex items-center gap-4 border-t border-white/20 pt-8">
                    <div class="flex -space-x-3">
                        <div class="w-12 h-12 rounded-full border-2 border-[#0a0a0a] bg-terracotta-600 flex items-center justify-center text-sm font-bold text-white shadow-lg">AS</div>
                        <div class="w-12 h-12 rounded-full border-2 border-[#0a0a0a] bg-gray-800 flex items-center justify-center text-sm font-bold text-white shadow-lg">JD</div>
                        <div class="w-12 h-12 rounded-full border-2 border-[#0a0a0a] bg-emerald-700 flex items-center justify-center text-sm font-bold text-white shadow-lg">MK</div>
                    </div>
                    <div class="text-sm font-medium text-gray-400">
                        Bergabung dengan <span class="text-white">Manajemen Logistik</span> SMKN 20.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.fade-img {
    opacity: 0.6;
}
.fade-enter-active,
.fade-leave-active {
    transition: opacity 1.5s ease-in-out;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
