<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import {
    Chart,
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';

Chart.register(LineController, LineElement, PointElement, LinearScale, CategoryScale, Tooltip, Legend, Filler);

const props = defineProps({
    readings: { type: Array, default: () => [] },
});

const canvas = ref(null);
let chart = null;

function labels() {
    return props.readings.map((r) =>
        new Date(r.recorded_at).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })
    );
}

function build() {
    if (!canvas.value) return;
    chart = new Chart(canvas.value, {
        type: 'line',
        data: {
            labels: labels(),
            datasets: [
                {
                    label: 'Velocidade (km/h)',
                    data: props.readings.map((r) => r.speed),
                    borderColor: '#38bdf8',
                    backgroundColor: 'rgba(56,189,248,0.15)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 0,
                    borderWidth: 2,
                },
                {
                    label: 'Combustível (%)',
                    data: props.readings.map((r) => r.fuel),
                    borderColor: '#34d399',
                    backgroundColor: 'rgba(52,211,153,0.1)',
                    fill: false,
                    tension: 0.35,
                    pointRadius: 0,
                    borderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            scales: {
                x: { ticks: { color: '#64748b', maxTicksLimit: 6 }, grid: { color: 'rgba(148,163,184,0.08)' } },
                y: { beginAtZero: true, max: 100, ticks: { color: '#64748b' }, grid: { color: 'rgba(148,163,184,0.08)' } },
            },
            plugins: {
                legend: { labels: { color: '#cbd5e1', boxWidth: 12 } },
            },
        },
    });
}

watch(
    () => props.readings,
    () => {
        if (!chart) return;
        chart.data.labels = labels();
        chart.data.datasets[0].data = props.readings.map((r) => r.speed);
        chart.data.datasets[1].data = props.readings.map((r) => r.fuel);
        chart.update('none');
    }
);

onMounted(build);
onBeforeUnmount(() => chart?.destroy());
</script>

<template>
    <div class="relative h-64">
        <canvas ref="canvas"></canvas>
    </div>
</template>
