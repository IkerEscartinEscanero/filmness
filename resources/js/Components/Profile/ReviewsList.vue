<script setup>
defineProps({
    reviews: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <section class="overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70 p-6 md:p-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-yellow-300/80">Reseñas</p>
            <h2 class="mt-2 text-2xl font-semibold text-white">Mis reseñas</h2>
        </div>

        <div v-if="reviews.length" class="mt-6 space-y-4">
            <article v-for="review in reviews" :key="review.id" class="rounded-2xl border border-white/10 bg-slate-800/70 p-4">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full border border-white/20 bg-slate-900">
                        <img
                            v-if="review.filmLogo"
                            :src="review.filmLogo"
                            :alt="review.filmTitle"
                            class="h-full w-full object-cover"
                        />
                        <span v-else>🎞️</span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                            <p class="font-semibold text-white">{{ review.filmTitle }}</p>
                            <p class="text-xs text-slate-400">{{ review.date }}</p>
                            <p class="stars-yellow text-sm text-yellow-300">{{ '★'.repeat(review.stars ?? 0) }}</p>
                        </div>
                        <p class="mt-2 text-sm text-slate-300">{{ review.comment || 'Sin comentario' }}</p>
                    </div>
                </div>
            </article>
        </div>

        <p v-else class="mt-5 rounded-2xl border border-dashed border-white/20 bg-slate-800/40 p-4 text-sm text-slate-400">
            Aún no has publicado ninguna reseña.
        </p>
    </section>
</template>