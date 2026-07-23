<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import ApexCharts from 'apexcharts';

const props = defineProps({
    categories: { type: Array, default: () => [] },
    series: { type: Array, default: () => [] },
    height: { type: Number, default: 350 },
    type: { type: String, default: 'area' }, // 'area', 'line', 'bar'
});

const chartContainer = ref(null);
let chart = null;

const renderChart = () => {
    if (chart) {
        chart.destroy();
    }

    if (!chartContainer.value) return;

    const options = {
        chart: {
            height: props.height,
            type: props.type,
            toolbar: { show: false },
            fontFamily: 'Lato, sans-serif',
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
            }
        },
        colors: ['#FF9F1C', '#2F2E5E', '#4F8CC9'],
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        series: props.series,
        xaxis: {
            categories: props.categories,
            labels: {
                style: {
                    colors: '#8d98b0',
                    fontSize: '12px'
                }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#8d98b0',
                    fontSize: '12px'
                }
            }
        },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 4,
            xaxis: { lines: { show: true } }
        },
        tooltip: {
            theme: 'light',
            x: { show: true },
            marker: { show: true }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            labels: { colors: '#202547' }
        }
    };

    chart = new ApexCharts(chartContainer.value, options);
    chart.render();
};

watch(() => [props.series, props.categories], () => {
    renderChart();
}, { deep: true });

onMounted(() => {
    renderChart();
});

onUnmounted(() => {
    if (chart) {
        chart.destroy();
    }
});
</script>

<template>
    <div ref="chartContainer" class="w-full"></div>
</template>
