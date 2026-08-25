<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="block w-full px-6 py-4 border border-black/10 rounded-full bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black text-sm transition-all duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] shadow-[0_4px_20px_rgb(0,0,0,0.03)]"
        v-model="model"
        ref="input"
    />
</template>
