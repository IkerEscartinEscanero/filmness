<script setup>
import { computed } from 'vue';
import FilmCarousel from '@/Components/FilmCarousel.vue';
import MovieCard from '@/Components/MovieCard.vue';

const props = defineProps({
    eyebrow: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    films: {
        type: Array,
        default: () => [],
    },
    emptyMessage: {
        type: String,
        required: true,
    },
    showDate: {
        type: Boolean,
        default: false,
    },
    useCarousel: {
        type: Boolean,
        default: false,
    },
    sectionClass: {
        type: String,
        default: 'mt-16',
    },
});

// This tells the template if it should render cards or the empty message.
const hasFilms = computed(() => props.films.length > 0);
</script>

<template>
    <section :class="sectionClass">
        <p class="text-sm uppercase tracking-[0.4em] text-yellow-300">{{ eyebrow }}</p>
        <h2 class="mt-2 text-3xl font-semibold text-white">{{ title }}</h2>

        <template v-if="hasFilms">
            <!-- Home uses the carousel on mobile/tablet and the grid on desktop. -->
            <FilmCarousel
                v-if="useCarousel"
                :films="films"
                :show-date="showDate"
            />

            <!-- The grid is shared by Home desktop and the other public pages. -->
            <div
                class="mt-8 grid grid-cols-2 gap-8 justify-items-center sm:grid-cols-4"
                :class="{ 'hidden lg:grid': useCarousel }"
            >
                <MovieCard
                    v-for="film in films"
                    :key="film.id"
                    :movie="film"
                    :show-date="showDate"
                />
            </div>
        </template>

        <p v-else class="mt-6 text-slate-400">{{ emptyMessage }}</p>
    </section>
</template>