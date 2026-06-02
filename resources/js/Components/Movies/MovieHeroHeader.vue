<script setup>


const props = defineProps({
    film: { type: Object, required: true },
    posterUrl: { type: String, default: null },
    logoUrl: { type: String, default: null },
    isAdmin: { type: Boolean, default: false },
    averageStars: { type: Number, default: null },
});

const emit = defineEmits(['edit']);

const starsLabel = (avg) => {
    const rounded = Math.round(avg);
    return '★'.repeat(rounded) + '☆'.repeat(Math.max(0, 5 - rounded));
};

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

        <div class="absolute bottom-0 left-0 right-0 max-w-7xl mx-auto px-4 sm:px-6 pb-8 flex items-end gap-3 sm:gap-6 min-w-0">
            <img
                v-if="logoUrl"
                :src="logoUrl"
                class="w-[clamp(88px,20vw,240px)] h-auto max-h-[96px] sm:max-h-[110px] md:max-h-[120px] flex-shrink rounded-xl border border-white/10 shadow-xl object-contain bg-slate-900/60"
            />
            <div class="min-w-0 flex-1">
                <h1 class="movie-title-fixed text-4xl font-bold text-white drop-shadow">{{ film.title }}</h1>
                <div class="flex items-center gap-3 mt-1 text-slate-400 text-sm">
                    <span v-if="film.genre" class="movie-genre-badge bg-yellow-500/20 text-yellow-400 px-2 py-0.5 rounded-full text-xs font-medium">{{ film.genre }}</span>
                    <span v-if="film.duration">{{ film.duration }} min</span>
                    <span v-if="film.release_date">{{ formatDate(film.release_date) }}</span>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <template v-if="averageStars !== null">
                        <span class="movie-rating-stars stars-yellow text-yellow-400 text-lg tracking-wider">{{ starsLabel(averageStars) }}</span>
                        <span class="movie-rating-value text-slate-300 text-xs">{{ averageStars.toFixed(1) }} / 5</span>
                    </template>
                    <span v-else class="movie-rating-empty text-slate-500 text-xs italic">No hay valoración disponible</span>
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