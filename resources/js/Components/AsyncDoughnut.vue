<script setup>
import { Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement);

defineProps(['data', 'options']);
</script>
<template>
    <Doughnut :data="data" :options="options" aria-label="Persentase stok berdasarkan kategori" />
</template>
