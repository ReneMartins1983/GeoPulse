<script setup>
defineProps({
    label: String,
    value: [String, Number],
    sub: String,
    accent: { type: String, default: 'text-slate-100' },
});
</script>

<template>
    <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4">
        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ label }}</p>
        <p class="mt-1 text-3xl font-bold" :class="accent">{{ value }}</p>
        <p v-if="sub" class="mt-0.5 text-xs text-slate-500">{{ sub }}</p>
    </div>
</template>
