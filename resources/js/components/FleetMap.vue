<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import L from 'leaflet';

const props = defineProps({
    vehicles: { type: Array, default: () => [] },
    selectedId: { type: Number, default: null },
});
const emit = defineEmits(['select']);

const STATUS_COLORS = {
    moving: '#22c55e',
    idle: '#f59e0b',
    stopped: '#ef4444',
    maintenance: '#6366f1',
};

const mapEl = ref(null);
let map = null;
const markers = new Map(); // id -> L.circleMarker

function render() {
    if (!map) return;
    const seen = new Set();

    for (const v of props.vehicles) {
        seen.add(v.id);
        const color = STATUS_COLORS[v.status] || '#94a3b8';
        let marker = markers.get(v.id);

        if (!marker) {
            marker = L.circleMarker([v.lat, v.lng], {
                radius: 7,
                weight: 2,
                color: '#0f172a',
                fillColor: color,
                fillOpacity: 0.95,
            }).addTo(map);
            marker.on('click', () => emit('select', v.id));
            markers.set(v.id, marker);
        } else {
            marker.setLatLng([v.lat, v.lng]);
            marker.setStyle({ fillColor: color });
        }

        marker.setRadius(v.id === props.selectedId ? 11 : 7);
        marker.bindTooltip(
            `${v.name} • ${v.speed} km/h`,
            { direction: 'top', offset: [0, -6] }
        );
    }

    // remove marcadores de veículos que sumiram
    for (const [id, marker] of markers) {
        if (!seen.has(id)) {
            map.removeLayer(marker);
            markers.delete(id);
        }
    }
}

onMounted(() => {
    map = L.map(mapEl.value, { zoomControl: true }).setView([-29.6783, -51.1306], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19,
    }).addTo(map);
    render();
});

onBeforeUnmount(() => map?.remove());

watch(() => props.vehicles, render, { deep: true });
watch(() => props.selectedId, render);
</script>

<template>
    <div ref="mapEl" class="h-[420px] w-full rounded-xl"></div>
</template>
