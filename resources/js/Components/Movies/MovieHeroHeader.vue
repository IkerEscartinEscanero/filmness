<script setup>
import { ref } from 'vue';

const props = defineProps({
    film: { type: Object, required: true },
    posterUrl: { type: String, default: null },
    logoUrl: { type: String, default: null },
    isAdmin: { type: Boolean, default: false },
});

const emit = defineEmits(['edit']);

const userRating = ref(0);
const hoverRating = ref(0);

const formatDate = (date) => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>

<template>
    <div class="relative h-[420px] overflow-hidden">
        <img
            v-if="posterUrl"
            :src="posterUrl"
            class="absolute inset-0 w-full h-full object-cover object-top"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A] via-[#0F172A]/60 to-transparent"></div>

        <div class="absolute bottom-0 left-0 right-0 max-w-7xl mx-auto px-6 pb-8 flex items-end gap-6">
            <img
                v-if="logoUrl"
                :src="logoUrl"
                class="h-25 max-w-[240px] w-auto rounded-xl border border-white/10 shadow-xl object-contain bg-slate-900/60"
            />
            <div>
                <h1 class="text-4xl font-bold text-white drop-shadow">{{ film.title }}</h1>
                <div class="flex items-center gap-3 mt-1 text-slate-400 text-sm">
                    <span v-if="film.genre" class="bg-yellow-500/20 text-yellow-400 px-2 py-0.5 rounded-full text-xs font-medium">{{ film.genre }}</span>
                    <span v-if="film.duration">{{ film.duration }} min</span>
                    <span v-if="film.release_date">{{ formatDate(film.release_date) }}</span>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <button
                        v-for="star in 5"
                        :key="star"
                        @mouseenter="hoverRating = star"
                        @mouseleave="hoverRating = 0"
                        @click="userRating = star"
                        class="text-xl transition-transform hover:scale-110"
                        :class="star <= (hoverRating || userRating) ? 'text-yellow-400' : 'text-slate-600'"
                    >★</button>
                    <span class="text-slate-400 text-xs ml-1">{{ userRating > 0 ? `${userRating}/5` : '0/5' }}</span>
                </div>
            </div>

            <div v-if="isAdmin" class="ml-auto flex gap-2">
                <button
                    @click="emit('edit')"
                    class="cursor-pointer px-4 py-2 rounded-xl bg-yellow-500 text-slate-950 font-semibold text-sm hover:bg-yellow-400 transition"
                >Editar</button>
            </div>
        </div>
    </div>
</template>