<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';
import StatCard from './StatCard.vue';
import FleetMap from './FleetMap.vue';
import SpeedChart from './SpeedChart.vue';

const vehicles = ref([]);
const stats = ref({});
const readings = ref([]);
const selectedId = ref(null);
const updatedAgo = ref(0);
let pollTimer = null;
let agoTimer = null;

const STATUS = {
    moving: { label: 'Em movimento', color: 'bg-green-500' },
    idle: { label: 'Ocioso', color: 'bg-amber-500' },
    stopped: { label: 'Parado', color: 'bg-red-500' },
    maintenance: { label: 'Manutenção', color: 'bg-indigo-500' },
};

const selected = computed(() => vehicles.value.find((v) => v.id === selectedId.value) || null);

async function fetchData() {
    const [s, v] = await Promise.all([
        axios.get('/api/stats'),
        axios.get('/api/vehicles'),
    ]);
    stats.value = s.data;
    vehicles.value = v.data.data;
    if (selectedId.value === null && vehicles.value.length) {
        selectVehicle(vehicles.value[0].id);
    } else if (selectedId.value !== null) {
        fetchReadings(selectedId.value);
    }
    updatedAgo.value = 0;
}

async function fetchReadings(id) {
    const { data } = await axios.get(`/api/vehicles/${id}/readings`);
    readings.value = data.data;
}

function selectVehicle(id) {
    selectedId.value = id;
    fetchReadings(id);
}

onMounted(() => {
    fetchData();
    pollTimer = setInterval(fetchData, 5000);
    agoTimer = setInterval(() => updatedAgo.value++, 1000);
});

onBeforeUnmount(() => {
    clearInterval(pollTimer);
    clearInterval(agoTimer);
});
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <!-- Top bar -->
        <header class="border-b border-slate-800 bg-slate-900/60 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-500 font-bold text-white">G</span>
                    <div>
                        <h1 class="text-lg font-bold leading-none">Geo<span class="text-sky-400">Pulse</span></h1>
                        <p class="text-xs text-slate-400">Monitoramento de frota em tempo real</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-sm text-slate-400">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-green-500"></span>
                    </span>
                    ao vivo · atualizado há {{ updatedAgo }}s
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl space-y-5 px-4 py-6">
            <!-- KPIs -->
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-5">
                <StatCard label="Veículos" :value="stats.total ?? '—'" />
                <StatCard label="Em movimento" :value="stats.moving ?? '—'" accent="text-green-400" />
                <StatCard label="Parados/ociosos" :value="(stats.idle ?? 0) + (stats.stopped ?? 0)" accent="text-amber-400" />
                <StatCard label="Vel. média" :value="(stats.avg_speed ?? 0) + ' km/h'" accent="text-sky-400" />
                <StatCard label="Comb. médio" :value="(stats.avg_fuel ?? 0) + '%'" accent="text-emerald-400" />
            </div>

            <!-- Mapa + lista -->
            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-2 lg:col-span-2">
                    <FleetMap :vehicles="vehicles" :selected-id="selectedId" @select="selectVehicle" />
                </div>

                <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4">
                    <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-400">Frota</h2>
                    <ul class="max-h-[380px] space-y-1 overflow-y-auto pr-1">
                        <li v-for="v in vehicles" :key="v.id">
                            <button
                                type="button"
                                @click="selectVehicle(v.id)"
                                class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm transition"
                                :class="v.id === selectedId ? 'bg-slate-800' : 'hover:bg-slate-800/50'"
                            >
                                <span class="flex items-center gap-2">
                                    <span class="h-2.5 w-2.5 rounded-full" :class="STATUS[v.status]?.color"></span>
                                    <span class="font-medium">{{ v.name }}</span>
                                </span>
                                <span class="text-slate-400">{{ v.speed }} km/h</span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Gráfico do veículo selecionado -->
            <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">
                        Telemetria
                        <span v-if="selected" class="ml-1 normal-case text-slate-200">· {{ selected.name }}</span>
                    </h2>
                    <span v-if="selected" class="flex items-center gap-2 text-xs text-slate-400">
                        <span class="h-2 w-2 rounded-full" :class="STATUS[selected.status]?.color"></span>
                        {{ STATUS[selected.status]?.label }} · {{ selected.driver }} · {{ selected.plate }}
                    </span>
                </div>
                <SpeedChart :readings="readings" />
            </div>
        </main>
    </div>
</template>
