<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

// Props to load the movie data
const props = defineProps({
    movie: Object,
    showDate: {
        type: Boolean,
        default: false,
    },
});

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
};

// Attribute to know when to modify design aspects when hovering over a movie
const showPreview = ref(false);
const posterUrl = computed(() => resolveStoragePath(props.movie?.poster));
const trailerUrl = computed(() => resolveStoragePath(props.movie?.trailer));
let timeout = null;

const resolveStoragePath = (path) => {
    if (!path) return null;
    if (path.startsWith('/storage/')) {
        return path;
    }
    if (path.startsWith('storage/')) {
        return `/${path}`;
    }
    return `/storage/${path}`;
};

const handleEnter = () => {
    timeout = setTimeout(() => {
        showPreview.value = true;
    }, 600);
};

const handleLeave = () => {
    showPreview.value = false;
    clearTimeout(timeout);
};

const goToMovie = () => {
    router.visit(`/movies/${props.movie.id}`);
};
</script>

<template>
    <div
        class="relative w-full max-w-[240px] aspect-[2/3] overflow-hidden rounded-3xl border border-white/10 bg-zinc-950 shadow-2xl shadow-black/30 transition-transform duration-300 hover:-translate-y-1 hover:scale-[1.02] hover:shadow-primary/30 group cursor-pointer"
        @mouseenter="handleEnter"
        @mouseleave="handleLeave"
        @click="goToMovie"
    >
        <img
            v-if="posterUrl"
            :src="posterUrl"
            class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105"
        />
        <div v-else class="absolute inset-0 bg-slate-900 flex items-center justify-center text-slate-400 text-sm">Sin imagen</div>

        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>

        <div class="absolute inset-0 bg-black/20 opacity-0 transition duration-300 group-hover:opacity-100"></div>

        <!-- Release date badge (top-right corner, shown only when showDate=true) -->
        <div
            v-if="showDate && movie.release_date"
            class="absolute top-3 right-3 rounded-full bg-yellow-500/90 backdrop-blur-sm px-3 py-1 text-xs font-semibold text-slate-900 shadow-md"
        >
            {{ formatDate(movie.release_date) }}
        </div>

        <div class="absolute bottom-0 left-0 right-0 p-4">
        <div class="rounded-3xl border border-white/10 bg-black/60 px-4 py-3 backdrop-blur-xl">
            <h2 class="text-white text-lg font-semibold leading-tight">
            {{ movie.title }}
            </h2>
        </div>
        </div>
    </div>

    <div
        v-if="showPreview && trailerUrl"
        class="fixed inset-0 flex items-center justify-center z-50 pointer-events-none"
    >
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div class="relative w-[60%] max-w-4xl rounded-3xl overflow-hidden shadow-2xl">
        <video
            :src="trailerUrl"
            autoplay
            loop
            class="w-full h-full object-cover"
        />
        </div>
    </div>
</template>