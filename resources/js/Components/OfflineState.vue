<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { PhWifiSlash } from '@phosphor-icons/vue';

const isOffline = ref(!navigator.onLine);

const handleOffline = () => {
    isOffline.value = true;
};

const handleOnline = () => {
    isOffline.value = false;
};

onMounted(() => {
    window.addEventListener('offline', handleOffline);
    window.addEventListener('online', handleOnline);
});

onUnmounted(() => {
    window.removeEventListener('offline', handleOffline);
    window.removeEventListener('online', handleOnline);
});
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-4"
    >
        <div 
            v-if="isOffline" 
            class="fixed inset-0 z-[9999] bg-white/95 backdrop-blur-xl flex flex-col items-center justify-center p-6"
        >
            <div class="max-w-md w-full text-center space-y-6">
                <!-- Icon -->
                <div class="mx-auto w-20 h-20 bg-gray-50 border border-black/5 rounded-[1.5rem] flex items-center justify-center shadow-sm">
                    <PhWifiSlash class="w-8 h-8 text-black" weight="duotone" />
                </div>
                
                <!-- Text -->
                <div class="space-y-2">
                    <h1 class="text-3xl md:text-4xl tracking-tighter leading-none font-bold tracking-tight text-black leading-none">
                        Koneksi Terputus
                    </h1>
                    <p class="text-base text-gray-500 leading-relaxed">
                        Anda sedang tidak terhubung ke internet. Aplikasi WMS membutuhkan koneksi jaringan untuk menyinkronkan data secara langsung.
                    </p>
                </div>

                <!-- Animated loading indicator representing waiting for signal -->
                <div class="pt-8 flex items-center justify-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-terracotta-500 animate-bounce" style="animation-delay: 0ms"></div>
                    <div class="w-2 h-2 rounded-full bg-terracotta-500 animate-bounce" style="animation-delay: 150ms"></div>
                    <div class="w-2 h-2 rounded-full bg-terracotta-500 animate-bounce" style="animation-delay: 300ms"></div>
                </div>
                
                <p class="text-sm font-medium text-gray-400 mt-4 uppercase tracking-widest">
                    Menunggu jaringan...
                </p>
            </div>
        </div>
    </Transition>
</template>
