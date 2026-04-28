<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    session: Object, // { id, date, price, room }
    film: Object,
    seats: Array, // [{ id, row, number, occupied }]
    initialSeatIds: { type: Array, default: () => [] },
});

// Helpers for formatting and display
const formatDateTime = (iso) => {
    const d = new Date(iso);
    return d.toLocaleDateString('es-ES', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
    }) + ' · ' + d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
};

// Seat selection state and logic. If we come back from the checkout, restore the seats
const selectedSeatIds = ref(new Set(props.initialSeatIds));

function toggleSeat(seat) {
    if (seat.occupied) return;
    const next = new Set(selectedSeatIds.value);
    if (next.has(seat.id)) {
        next.delete(seat.id);
    } else {
        next.add(seat.id);
    }
    selectedSeatIds.value = next;
}

function seatClass(seat) {
    if (seat.occupied) {
        return 'bg-red-700/80 cursor-not-allowed border-red-800 text-red-300';
    }
    if (selectedSeatIds.value.has(seat.id)) {
        return 'bg-yellow-500 border-yellow-400 text-slate-950 cursor-pointer scale-105';
    }
    return 'bg-slate-700 border-slate-500 text-slate-300 hover:bg-emerald-600/70 hover:border-emerald-500 cursor-pointer';
}

// Seat grid grouped by row
const rows = computed(() => {
    const map = {};
    for (const seat of props.seats) {
        if (!map[seat.row]) map[seat.row] = [];
        map[seat.row].push(seat);
    }
    // Sort by row label, then by number within each row
    return Object.keys(map)
        .sort()
        .map(row => ({ row, seats: map[row].sort((a, b) => a.number - b.number) }));
});

// Summary of selected seats and total price
const selectedCount = computed(() => selectedSeatIds.value.size);
const totalPrice = computed(() => (selectedCount.value * props.session.price).toFixed(2));

const selectedSeats = computed(() =>
    props.seats
        .filter(s => selectedSeatIds.value.has(s.id))
        .map(s => `${s.row}${s.number}`)
        .join(', ')
);

function clearSelection() {
    selectedSeatIds.value = new Set();
}

function goToCheckout() {
    if (selectedCount.value === 0) return;

    router.get(route('checkout.create', { session: props.session.id }), {
        seat_ids: Array.from(selectedSeatIds.value),
    });
}

function goBack() {
    router.visit(`/movies/${props.film.id}`);
}
</script>

<template>
    <Layout>
        <Head title="Selección de butacas" />

        <main class="min-h-screen bg-[#0F172A] text-white py-10 px-4">
            <div class="max-w-4xl mx-auto flex flex-col gap-8">

                <!-- Header -->
                <div class="flex items-start sm:items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <button
                            @click="goBack"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-600 bg-slate-800 hover:bg-slate-700 transition text-slate-200 hover:text-white text-sm font-medium"
                            title="Volver a horarios"
                            aria-label="Volver a horarios"
                        >
                            <span>🢀 Volver a horarios</span>
                        </button>
                    </div>

                    <div class="grow sm:grow-0">
                        <h1 class="text-2xl font-bold text-white">{{ film.title }}</h1>
                        <p class="text-right text-slate-400 text-sm mt-0.5">
                            {{ formatDateTime(session.date) }} &nbsp;·&nbsp;
                            <span class="text-slate-300">{{ session.room }}</span>
                        </p>
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex items-center gap-6 text-sm text-slate-400">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-slate-700 border border-slate-500 inline-block"></span>
                        Disponible
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-yellow-500 border border-yellow-400 inline-block"></span>
                        Seleccionada
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-red-700/80 border border-red-800 inline-block"></span>
                        Ocupada
                    </div>
                </div>

                <!-- Screen indicator -->
                <div class="flex flex-col items-center gap-1">
                    <div class="w-3/4 h-2 bg-yellow-900"></div>
                    <p class="text-xs text-slate-500 uppercase tracking-widest">Pantalla</p>
                </div>

                <!-- Seat grid -->
                <div class="flex flex-col items-center gap-2">
                    <div
                        v-for="{ row, seats: rowSeats } in rows"
                        :key="row"
                        class="flex items-center gap-2"
                    >
                        <!-- Row label -->
                        <span class="w-5 text-center text-xs font-bold text-slate-500">{{ row }}</span>

                        <!-- Seats -->
                        <div class="flex gap-1.5">
                            <!-- Aisle after 5th seat -->
                            <template v-for="seat in rowSeats" :key="seat.id">
                                <div
                                    class="w-8 h-8 rounded-md border text-xs font-semibold flex items-center justify-center transition-all duration-150"
                                    :class="seatClass(seat)"
                                    @click="toggleSeat(seat)"
                                    :title="`Fila ${seat.row} – Butaca ${seat.number}`"
                                >
                                    {{ seat.number }}
                                </div>
                                <!-- Middle aisle gap after seat 5 -->
                                <div v-if="seat.number === 5" class="w-4"></div>
                            </template>
                        </div>

                        <span class="w-5 text-center text-xs font-bold text-slate-500">{{ row }}</span>
                    </div>
                </div>

                <!-- Selection summary & CTA -->
                <div class="bg-slate-800/70 rounded-2xl border border-white/5 p-6 flex flex-col gap-4">
                    <div v-if="selectedCount === 0" class="text-slate-500 text-sm text-center">
                        Selecciona las butacas que deseas reservar.
                    </div>

                    <template v-else>
                        <div class="flex flex-wrap gap-4 justify-between items-center">
                            <div>
                                <p class="text-slate-400 text-xs uppercase tracking-widest mb-1">Butacas seleccionadas</p>
                                <p class="text-white font-semibold text-sm">{{ selectedSeats }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-slate-400 text-xs uppercase tracking-widest mb-1">Total</p>
                                <p class="text-yellow-400 font-bold text-xl">{{ totalPrice }} €</p>
                                <p class="text-slate-500 text-xs">{{ selectedCount }} × {{ session.price.toFixed(2) }} €</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button
                                @click="clearSelection"
                                class="cursor-pointer flex-1 py-2.5 rounded-xl border border-slate-600 text-slate-400 text-sm hover:border-slate-400 hover:text-white transition"
                            >
                                Limpiar selección
                            </button>
                            <button
                                @click="goToCheckout"
                                class="cursor-pointer flex-[2] py-2.5 rounded-xl bg-yellow-500 text-slate-950 font-bold text-sm hover:bg-yellow-400 transition shadow-lg shadow-yellow-500/20"
                            >
                                Continuar con la compra
                            </button>
                        </div>
                    </template>
                </div>

            </div>
        </main>
    </Layout>
</template>