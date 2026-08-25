<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full px-6 py-4 rounded-full text-start text-base font-semibold text-terracotta-700 bg-terracotta-50 focus:outline-none focus:text-terracotta-800 focus:bg-terracotta-100 transition duration-150 ease-in-out'
        : 'block w-full px-6 py-4 rounded-full text-start text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-black/5 focus:outline-none focus:text-gray-900 focus:bg-black/5 transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
