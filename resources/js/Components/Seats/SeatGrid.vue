<script setup>
import { computed } from 'vue';

const props = defineProps({
    seats: { type: Array, default: () => [] },
    selectedSeatIds: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
});

const emit = defineEmits(['seat-click']);

function isSeatSelected(seatId) {
    return props.selectedSeatIds.includes(seatId);
}

function seatClass(seat) {
    if (seat.occupied) {
        return props.isAdmin
            ? 'bg-red-700/80 border-red-800 text-red-300 hover:bg-amber-600/80 hover:border-amber-500 cursor-pointer'
            : 'bg-red-700/80 cursor-not-allowed border-red-800 text-red-300';
    }

    if (isSeatSelected(seat.id)) {
        return 'bg-yellow-500 border-yellow-400 text-slate-950 cursor-pointer scale-105';
    }

    return 'bg-slate-700 border-slate-500 text-slate-300 hover:bg-emerald-600/70 hover:border-emerald-500 cursor-pointer';
}

const rows = computed(() => {
    const seatsByRow = {};

    for (const seat of props.seats) {
        if (!seatsByRow[seat.row]) {
            seatsByRow[seat.row] = [];
        }

        seatsByRow[seat.row].push(seat);
    }

    return Object.keys(seatsByRow)
        .sort()
        .map((row) => ({
            row,
            seats: seatsByRow[row].sort((a, b) => a.number - b.number),
        }));
});
</script>

<template>
    <div class="flex flex-col items-center gap-2">
        <div
            v-for="{ row, seats: rowSeats } in rows"
            :key="row"
            class="flex items-center gap-2"
        >
            <span class="w-5 text-center text-xs font-bold text-slate-500">{{ row }}</span>

            <div class="flex gap-1.5">
                <template v-for="seat in rowSeats" :key="seat.id">
                    <div
                        class="w-8 h-8 rounded-md border text-xs font-semibold flex items-center justify-center transition-all duration-150"
                        :class="seatClass(seat)"
                        @click="emit('seat-click', seat)"
                        :title="`Fila ${seat.row} – Butaca ${seat.number}`"
                    >
                        {{ seat.number }}
                    </div>
                    <div v-if="seat.number === 5" class="w-4"></div>
                </template>
            </div>

            <span class="w-5 text-center text-xs font-bold text-slate-500">{{ row }}</span>
        </div>
    </div>
</template>