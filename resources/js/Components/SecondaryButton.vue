<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center px-6 py-3 border border-black/10 rounded-full text-sm font-semibold text-black bg-white hover:bg-black/5 transition-all duration-700 ease-[cubic-bezier(0.32,0.72,0,1)] hover:scale-[0.98] shadow-sm focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 disabled:opacity-25"
    >
        <slot />
    </button>
</template>
