<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import SeatGrid from '@/Components/Seats/SeatGrid.vue';
import ReleaseSeatModal from '@/Components/Seats/ReleaseSeatModal.vue';
import SelectionSummary from '@/Components/Seats/SelectionSummary.vue';

const props = defineProps({
    session: Object, // { id, date, price, room }
    film: Object,
    seats: Array, // [{ id, row, number, occupied }]
    initialSeatIds: { type: Array, default: () => [] },
});

const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin');
const showReleaseModal = ref(false);
const seatPendingRelease = ref(null);

const formatDateTime = (iso) => {
    const d = new Date(iso);

    return d.toLocaleDateString('es-ES', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
    }) + ' · ' + d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
};

const selectedSeatIds = ref(new Set(props.initialSeatIds));

function toggleAvailableSeat(seat) {
    const next = new Set(selectedSeatIds.value);

    if (next.has(seat.id)) {
        next.delete(seat.id);
    } else {
        next.add(seat.id);
    }

    selectedSeatIds.value = next;
}

function handleSeatClick(seat) {
    if (seat.occupied) {
        if (isAdmin.value) openReleaseSeatModal(seat);
        return;
    }

    toggleAvailableSeat(seat);
}

const selectedCount = computed(() => selectedSeatIds.value.size);
const totalPrice = computed(() => (selectedCount.value * props.session.price).toFixed(2));
const selectedSeatIdList = computed(() => Array.from(selectedSeatIds.value));

const selectedSeatsText = computed(() =>
    props.seats
        .filter((seat) => selectedSeatIds.value.has(seat.id))
        .map((seat) => `${seat.row}${seat.number}`)
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

function openReleaseSeatModal(seat) {
    seatPendingRelease.value = seat;
    showReleaseModal.value = true;
}

function closeReleaseModal() {
    showReleaseModal.value = false;
    seatPendingRelease.value = null;
}

function confirmReleaseSeat() {
    if (!seatPendingRelease.value) {
        return;
    }

    const seat = seatPendingRelease.value;

    router.delete(route('sessions.seats.release', { session: props.session.id, seat: seat.id }), {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => closeReleaseModal(),
    });
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
                            {{ formatDateTime(session.date) }} &nbsp;·&nbsp; <span class="text-slate-300">{{ session.room }}</span>
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

                <div v-if="isAdmin" class="rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-200">
                    Pulsa una butaca ocupada para liberarla y volver a ponerla disponible.
                </div>

                <!-- Screen indicator -->
                <div class="flex flex-col items-center gap-1">
                    <div class="w-3/4 h-2 bg-yellow-900"></div>
                    <p class="text-xs text-slate-500 uppercase tracking-widest">Pantalla</p>
                </div>

                <SeatGrid
                    :seats="seats"
                    :selected-seat-ids="selectedSeatIdList"
                    :is-admin="isAdmin"
                    @seat-click="handleSeatClick"
                />

                <SelectionSummary
                    :selected-count="selectedCount"
                    :selected-seats-text="selectedSeatsText"
                    :total-price="totalPrice"
                    :seat-price="session.price"
                    @clear="clearSelection"
                    @checkout="goToCheckout"
                />

            </div>
        </main>

        <ReleaseSeatModal
            :show="showReleaseModal"
            :seat="seatPendingRelease"
            @cancel="closeReleaseModal"
            @confirm="confirmReleaseSeat"
        />
    </Layout>
</template>