<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    filmId: { type: Number, required: true },
    rooms: { type: Array, default: () => [] },
    errorMessage: { type: String, default: null },
});

const localError = ref(null);

const sessionForm = ref({
    room_id: '',
    date: '',
    time: '',
    price: '9.50',
});

const formError = computed(() => localError.value || props.errorMessage || null);

const formatDateInputValue = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const formatTimeInputValue = (date) => {
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${hours}:${minutes}`;
};

const minSessionDate = computed(() => formatDateInputValue(new Date()));
const minSessionTimeForToday = computed(() => formatTimeInputValue(new Date()));

// Fast client-side check. Backend validations remain the source of truth.
const isSessionFormInPast = computed(() => {
    if (!sessionForm.value.date || !sessionForm.value.time) return false;

    const selectedDateTime = new Date(`${sessionForm.value.date}T${sessionForm.value.time}`);

    return !Number.isNaN(selectedDateTime.getTime()) && selectedDateTime < new Date();
});

function createSession() {
    if (!sessionForm.value.room_id || !sessionForm.value.date || !sessionForm.value.time) return;

    if (isSessionFormInPast.value) {
        localError.value = 'No se pueden añadir horarios con fecha u hora pasadas.';
        return;
    }

    localError.value = null;

    router.post(`/films/${props.filmId}/sessions`, {
        room_id: sessionForm.value.room_id,
        date: `${sessionForm.value.date} ${sessionForm.value.time}:00`,
        price: Number(sessionForm.value.price || 0),
    }, {
        preserveScroll: true,
        onSuccess: () => {
            localError.value = null;
            sessionForm.value.date = '';
            sessionForm.value.time = '';
        },
    });
}
</script>

<template>
    <div class="bg-slate-800/60 rounded-2xl border border-white/5 p-5 flex flex-col gap-4 mb-4">
        <h3 class="text-sm font-semibold text-yellow-400 uppercase tracking-widest">Gestión de horarios</h3>

        <p
            v-if="formError"
            class="rounded-xl border border-red-500/40 bg-red-500/10 px-3 py-2 text-sm text-red-300"
        >
            {{ formError }}
        </p>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="flex flex-col gap-1.5">
                <label for="session-room" class="font-medium text-slate-300">Sala</label>
                <select
                    id="session-room"
                    v-model="sessionForm.room_id"
                    class="rounded-xl bg-slate-700 border border-slate-600 text-slate-100 px-3 py-2 text-sm focus:outline-none focus:border-yellow-500"
                >
                    <option disabled value="">Escoge una sala</option>
                    <option v-for="room in rooms" :key="room.id" :value="room.id">{{ room.name }}</option>
                </select>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="session-date" class="font-medium text-slate-300">Fecha</label>
                <input
                    id="session-date"
                    v-model="sessionForm.date"
                    type="date"
                    :min="minSessionDate"
                    class="rounded-xl bg-slate-700 border border-slate-600 text-slate-100 px-3 py-2 text-sm focus:outline-none focus:border-yellow-500"
                />
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="session-time" class="font-medium text-slate-300">Hora</label>
                <input
                    id="session-time"
                    v-model="sessionForm.time"
                    type="time"
                    :min="sessionForm.date === minSessionDate ? minSessionTimeForToday : undefined"
                    class="rounded-xl bg-slate-700 border border-slate-600 text-slate-100 px-3 py-2 text-sm focus:outline-none focus:border-yellow-500"
                />
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="session-price" class="font-medium text-slate-300">Precio</label>
                <input
                    id="session-price"
                    v-model="sessionForm.price"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="Precio"
                    class="rounded-xl bg-slate-700 border border-slate-600 text-slate-100 px-3 py-2 text-sm focus:outline-none focus:border-yellow-500"
                />
            </div>
        </div>

        <button
            @click="createSession"
            class="cursor-pointer self-start px-4 py-2 rounded-xl bg-emerald-500 text-slate-950 font-semibold text-sm hover:bg-emerald-400 transition"
        >
            Añadir horario
        </button>
    </div>
</template>
