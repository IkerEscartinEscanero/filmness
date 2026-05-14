<script setup>
defineProps({
    discounts: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <section class="overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70 p-6 md:p-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-yellow-300/80">Descuentos</p>
            <h2 class="mt-2 text-2xl font-semibold text-white">Mis descuentos</h2>
        </div>

        <div v-if="discounts.length" class="mt-6 grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
            <article
                v-for="discount in discounts"
                :key="discount.key ?? discount.reason ?? discount.id"
                class="rounded-2xl border px-4 py-3"
                :class="discount.available
                    ? 'border-emerald-400/30 bg-emerald-500/10'
                    : 'border-slate-600/80 bg-slate-800/50 opacity-70'"
            >
                <div class="flex items-start justify-between gap-3">
                    <p class="font-medium text-white">{{ discount.label }}</p>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span
                        class="rounded-full px-2 py-1 text-xs font-semibold"
                        :class="discount.available ? 'bg-emerald-400/15 text-emerald-200' : 'bg-slate-700 text-slate-300'"
                    >
                        {{ discount.available ? 'Disponible' : 'No disponible' }}
                    </span>
                    <span
                        v-if="discount.reason !== 'large_purchase'"
                        class="rounded-full px-2 py-1 text-xs font-semibold"
                        :class="discount.used ? 'bg-amber-400/15 text-amber-200' : 'bg-sky-400/15 text-sky-200'"
                    >
                        {{ discount.used ? 'Usado o caducado' : 'Sin usar' }}
                    </span>
                </div>

                <p class="mt-2 text-sm text-yellow-300">{{ discount.value }}</p>
                <p v-if="discount.expiration_date" class="mt-1 text-xs text-slate-400">Caduca: {{ discount.expiration_date }}</p>
            </article>
        </div>

        <p v-else class="mt-5 rounded-2xl border border-dashed border-white/20 bg-slate-800/40 p-4 text-sm text-slate-400">
            No hay descuentos disponibles.
        </p>
    </section>
</template>