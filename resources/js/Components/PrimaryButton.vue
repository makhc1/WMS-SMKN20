<script setup>
defineProps({
    isLoading: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <button
        :aria-busy="isLoading"
        class="inline-flex relative items-center justify-center px-6 py-3 border border-transparent rounded-full text-sm font-semibold text-white bg-terracotta-600 hover:bg-terracotta-700 transition-all duration-700 ease-[cubic-bezier(0.32,0.72,0,1)] hover:scale-[0.98] shadow-[0_8px_30px_rgb(193,83,53,0.3)] focus:outline-none focus:ring-2 focus:ring-terracotta-600 focus:ring-offset-2 disabled:opacity-75 disabled:cursor-not-allowed"
    >
        <span :class="{ 'opacity-0': isLoading }" class="flex items-center gap-3">
            <slot />
        </span>
        
        <svg v-if="isLoading" class="animate-spin absolute h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </button>
</template>
