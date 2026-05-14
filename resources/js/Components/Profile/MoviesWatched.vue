<script setup>
defineProps({
    movies: {
        type: Array,
        default: () => [],
    },
});

const resolveMediaPath = (path) => {
    if (!path) {
        return null;
    }

    if (path.startsWith('http://') || path.startsWith('https://')) {
        return path;
    }

    if (path.startsWith('/')) {
        return path;
    }

    return path.startsWith('storage/') ? `/${path}` : `/storage/${path}`;
};
</script>

<template>
    <section class="overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70 p-6 md:p-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-yellow-300/80">Películas</p>
            <h2 class="mt-2 text-2xl font-semibold text-white">Películas vistas</h2>
        </div>

        <div v-if="movies.length" class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-5">
            <article v-for="movie in movies" :key="movie.id" class="overflow-hidden rounded-2xl border border-white/10 bg-slate-800/70">
                <img
                    v-if="movie.poster"
                    :src="resolveMediaPath(movie.poster)"
                    :alt="movie.title"
                    class="h-44 w-full object-cover"
                />
                <div v-else class="flex h-44 items-center justify-center bg-slate-700 text-4xl">🎬</div>

                <div class="p-3">
                    <p class="line-clamp-2 text-sm font-semibold text-white">{{ movie.title }}</p>
                    <p v-if="movie.watchedAt" class="mt-1 text-xs text-slate-400">Vista en: {{ movie.watchedAt }}</p>
                </div>
            </article>
        </div>

        <p v-else class="mt-5 rounded-2xl border border-dashed border-white/20 bg-slate-800/40 p-4 text-sm text-slate-400">
            Todavía no hay películas registradas.
        </p>
    </section>
</template>