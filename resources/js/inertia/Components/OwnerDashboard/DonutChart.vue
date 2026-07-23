<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import ApexCharts from 'apexcharts';

const props = defineProps({
    labels: { type: Array, default: () => [] },
    series: { type: Array, default: () => [] },
    height: { type: Number, default: 320 },
});

const chartContainer = ref(null);
let chart = null;

const renderChart = () => {
    if (chart) {
        chart.destroy();
    }

    if (!chartContainer.value || !props.series.length) return;

    const options = {
        chart: {
            height: props.height,
            type: 'donut',
            fontFamily: 'Lato, sans-serif',
        },
        colors: ['#FF9F1C', '#2F2E5E', '#4F8CC9', '#8F79D8', '#FF4D4D', '#2ECC71'],
        labels: props.labels,
        series: props.series,
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: (w) => {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString();
                            },
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold',
                                color: '#202547'
                            }
                        }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        legend: {
            position: 'bottom',
            labels: { colors: '#202547' }
        },
        tooltip: {
            theme: 'light'
        }
    };

    chart = new ApexCharts(chartContainer.value, options);
    chart.render();
};

watch(() => [props.series, props.labels], () => {
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
    <div ref="chartContainer" class="w-full flex justify-center"></div>
</template>
