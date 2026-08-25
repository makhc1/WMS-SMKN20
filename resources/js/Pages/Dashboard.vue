<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { PhCube, PhArrowDownLeft, PhArrowUpRight, PhClock } from '@phosphor-icons/vue';
import { computed, defineAsyncComponent } from 'vue';

const VueApexCharts = defineAsyncComponent(() => import('vue3-apexcharts'));

const props = defineProps({
    stats: Object,
    lowStock: Array,
    chartData: Array,
    activeItems: Array
});

// Area Chart Configuration (Inbound vs Outbound)
const areaSeries = computed(() => {
    if (!props.chartData) return [];
    return [
        {
            name: 'Inbound',
            data: props.chartData.map(d => parseInt(d.inbound) || 0)
        },
        {
            name: 'Outbound',
            data: props.chartData.map(d => parseInt(d.outbound) || 0)
        }
    ];
});

const areaOptions = computed(() => {
    return {
        chart: {
            type: 'area',
            fontFamily: 'inherit',
            toolbar: { show: false },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
            }
        },
        colors: ['#10B981', '#D36A49'], // Emerald and Terracotta
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.25, opacityTo: 0.05, stops: [0, 90, 100] }
        },
        xaxis: {
            categories: props.chartData ? props.chartData.map(d => d.date) : [],
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { colors: '#9CA3AF', fontSize: '11px', fontWeight: 500 } }
        },
        yaxis: {
            labels: {
                style: { colors: '#9CA3AF', fontSize: '11px', fontWeight: 500 },
                formatter: (value) => Math.round(value)
            },
        },
        grid: {
            borderColor: 'rgba(0,0,0,0.03)',
            strokeDashArray: 4,
            yaxis: { lines: { show: true } },
            xaxis: { lines: { show: false } },
            padding: { top: 0, right: 0, bottom: 0, left: 10 }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            offsetY: -20,
            itemMargin: { horizontal: 10, vertical: 0 },
            markers: { radius: 12 }
        },
        tooltip: {
            theme: 'light',
            y: { formatter: (val) => val + " Unit" },
            marker: { show: true },
        }
    };
});

// Bar Chart Configuration (Top Active Items)
const barSeries = computed(() => {
    if (!props.activeItems) return [];
    return [{
        name: 'Total Aktivitas',
        data: props.activeItems.map(item => item.activity)
    }];
});

const barOptions = computed(() => {
    return {
        chart: {
            type: 'bar',
            fontFamily: 'inherit',
            toolbar: { show: false }
        },
        colors: ['#D36A49'], // Terracotta
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
                distributed: true, // Color each bar differently if needed
            }
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: props.activeItems ? props.activeItems.map(item => item.name) : [],
            labels: { style: { colors: '#9CA3AF', fontSize: '11px', fontWeight: 500 } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: { colors: '#6B7280', fontSize: '12px', fontWeight: 600 },
            }
        },
        grid: {
            borderColor: 'rgba(0,0,0,0.03)',
            strokeDashArray: 4,
            xaxis: { lines: { show: true } },
            yaxis: { lines: { show: false } },
        },
        legend: { show: false },
        tooltip: {
            theme: 'light',
            y: { formatter: (val) => val + " Unit" }
        }
    };
});

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            Dashboard
        </template>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-8">
            
            <!-- Metric: Utilisasi Kapasitas -->
            <div class="md:col-span-3 bg-white border border-black/10 rounded-[1.5rem] overflow-hidden">
                <div class="bg-transparent p-6 h-full flex flex-col justify-between relative overflow-hidden">
                    <div class="flex justify-between items-start mb-8 relative z-10">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center">
                            <PhCube class="w-5 h-5 text-emerald-600" />
                        </div>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-end gap-2">
                            <h2 class="text-5xl font-bold tracking-tighter text-black">{{ stats?.capacity_utilization || 0 }}<span class="text-3xl text-gray-400">%</span></h2>
                        </div>
                        <p class="text-xs font-bold text-gray-600 uppercase tracking-widest mt-2">Kapasitas Terpakai</p>
                        <p class="text-[10px] text-gray-400 font-medium mt-1">{{ stats?.total_stock }} / 10.000 Unit</p>
                    </div>
                </div>
            </div>

            <!-- Metric: Inbound Today -->
            <div class="md:col-span-3 bg-white border border-black/10 rounded-[1.5rem] overflow-hidden">
                <div class="bg-transparent p-6 h-full flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-8">
                        <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center">
                            <PhArrowDownLeft class="w-5 h-5 text-indigo-600" />
                        </div>
                    </div>
                    <div>
                        <h2 class="text-5xl font-bold tracking-tighter text-black">{{ stats?.inbound_today || 0 }}</h2>
                        <p class="text-xs font-bold text-gray-600 uppercase tracking-widest mt-2">Inbound Hari Ini</p>
                    </div>
                </div>
            </div>

            <!-- Metric: Outbound Today -->
            <div class="md:col-span-3 bg-white border border-black/10 rounded-[1.5rem] overflow-hidden">
                <div class="bg-transparent p-6 h-full flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-8">
                        <div class="w-10 h-10 rounded-full bg-terracotta-50 flex items-center justify-center">
                            <PhArrowUpRight class="w-5 h-5 text-terracotta-600" />
                        </div>
                    </div>
                    <div>
                        <h2 class="text-5xl font-bold tracking-tighter text-black">{{ stats?.outbound_today || 0 }}</h2>
                        <p class="text-xs font-bold text-gray-600 uppercase tracking-widest mt-2">Outbound Hari Ini</p>
                    </div>
                </div>
            </div>
            
            <!-- Metric: Menunggu Diproses -->
            <div class="md:col-span-3 bg-white border border-black/10 rounded-[1.5rem] overflow-hidden">
                <div :class="[
                    'rounded-[1.625rem] p-6 h-full flex flex-col justify-between transition-colors duration-500',
                    (stats?.pending_tasks > 0) ? 'bg-amber-50' : 'bg-white'
                ]">
                    <div class="flex justify-between items-start mb-8">
                        <div :class="[
                            'w-10 h-10 rounded-full flex items-center justify-center',
                            (stats?.pending_tasks > 0) ? 'bg-amber-100' : 'bg-black/5'
                        ]">
                            <PhClock :class="['w-5 h-5', (stats?.pending_tasks > 0) ? 'text-amber-600 animate-pulse' : 'text-black']" />
                        </div>
                    </div>
                    <div>
                        <h2 :class="['text-5xl font-bold tracking-tighter', (stats?.pending_tasks > 0) ? 'text-amber-700' : 'text-black']">{{ stats?.pending_tasks || 0 }}</h2>
                        <p :class="['text-[10px] font-bold uppercase tracking-widest mt-2', (stats?.pending_tasks > 0) ? 'text-amber-600/80' : 'text-gray-600']">Menunggu Diproses</p>
                    </div>
                </div>
            </div>

            <!-- Chart: Line (ApexCharts) -->
            <div class="md:col-span-7 bg-white border border-black/10 rounded-[1.5rem] overflow-hidden">
                <div class="bg-transparent p-8 h-[400px] flex flex-col">
                    <div class="mb-2">
                        <h2 class="text-sm font-semibold tracking-tight text-black">Arus Barang (7 Hari Terakhir)</h2>
                    </div>
                    <div class="flex-1 w-full min-h-0 relative -ml-2">
                        <VueApexCharts 
                            type="area" 
                            height="100%" 
                            :options="areaOptions" 
                            :series="areaSeries" 
                        />
                    </div>
                </div>
            </div>

            <!-- Chart: Bar (ApexCharts) -->
            <div class="md:col-span-5 bg-white border border-black/10 rounded-[1.5rem] overflow-hidden">
                <div class="bg-transparent p-8 h-[400px] flex flex-col">
                    <div class="mb-6">
                        <h2 class="text-sm font-semibold tracking-tight text-black">Barang Paling Aktif (30 Hari)</h2>
                    </div>
                    <div class="flex-1 min-h-0 relative">
                        <VueApexCharts 
                            v-if="activeItems && activeItems.length"
                            type="bar" 
                            height="100%" 
                            :options="barOptions" 
                            :series="barSeries" 
                        />
                        <div v-else class="flex items-center justify-center h-full text-sm text-gray-400">Belum ada aktivitas barang</div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
