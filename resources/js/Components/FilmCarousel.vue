<script setup>
import { computed, ref, watch } from 'vue';
import MovieCard from '@/Components/MovieCard.vue';

const props = defineProps({
    films: {
        type: Array,
        default: () => [],
    },
    showDate: {
        type: Boolean,
        default: false,
    },
});

const activeIndex = ref(0);
const touchStartX = ref(null);

const totalFilms = computed(() => props.films.length);
const hasFilms = computed(() => totalFilms.value > 0);
const hasMultipleFilms = computed(() => totalFilms.value > 1);

const currentFilm = computed(() => {
    return props.films[activeIndex.value] ?? null;
});

const getValidIndex = (index) => {
    if (!totalFilms.value) {
        return 0;
    }

    return (index + totalFilms.value) % totalFilms.value;
};

const leftTabletFilm = computed(() => {
    if (totalFilms.value <= 2) {
        return null;
    }

    return props.films[getValidIndex(activeIndex.value - 1)] ?? null;
});

const rightTabletFilm = computed(() => {
    if (totalFilms.value <= 1) {
        return null;
    }

    return props.films[getValidIndex(activeIndex.value + 1)] ?? null;
});

const goNext = () => {
    if (!hasMultipleFilms.value) {
        return;
    }

    activeIndex.value = getValidIndex(activeIndex.value + 1);
};

const goPrev = () => {
    if (!hasMultipleFilms.value) {
        return;
    }

    activeIndex.value = getValidIndex(activeIndex.value - 1);
};

const handleTouchStart = (event) => {
    touchStartX.value = event.changedTouches[0]?.clientX ?? null;
};

const handleTouchEnd = (event) => {
    if (touchStartX.value === null) {
        return;
    }

    const endX = event.changedTouches[0]?.clientX ?? touchStartX.value;
    const delta = endX - touchStartX.value;
    touchStartX.value = null;

    if (Math.abs(delta) < 45) {
        return;
    }

    if (delta < 0) {
        goNext();
        return;
    }

    goPrev();
};

watch(
    () => props.films.length,
    (newLength) => {
        if (!newLength) {
            activeIndex.value = 0;
            return;
        }

        if (activeIndex.value >= newLength) {
            activeIndex.value = newLength - 1;
        }
    }
);
</script>

<template>
    <section
        v-if="hasFilms"
        class="px-4 sm:px-0 lg:hidden"
        @touchstart.passive="handleTouchStart"
        @touchend.passive="handleTouchEnd"
    >
        <div class="mt-8 md:hidden">
            <div class="relative mx-auto flex max-w-[21rem] items-center justify-center px-10">
                <div class="absolute inset-x-8 bottom-0 h-16 rounded-full bg-yellow-500/20 blur-3xl"></div>
                <MovieCard
                    v-if="currentFilm"
                    :movie="currentFilm"
                    :show-date="showDate"
                />

                <button
                    type="button"
                    class="absolute left-0 top-1/2 z-30 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full border border-yellow-500/20 bg-yellow-500 text-slate-950 shadow-xl shadow-yellow-950/30 transition hover:scale-105 disabled:cursor-not-allowed disabled:opacity-40"
                    :disabled="!hasMultipleFilms"
                    @click="goPrev"
                >
                    <span class="text-2xl leading-none">🢀</span>
                </button>

                <button
                    type="button"
                    class="absolute right-0 top-1/2 z-30 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full border border-yellow-500/20 bg-yellow-500 text-slate-950 shadow-xl shadow-yellow-950/30 transition hover:scale-105 disabled:cursor-not-allowed disabled:opacity-40"
                    :disabled="!hasMultipleFilms"
                    @click="goNext"
                >
                    <span class="text-2xl leading-none">🢂</span>
                </button>
            </div>
        </div>

        <div class="mt-10 hidden md:block">
            <div class="relative px-8">
                <div class="absolute left-0 top-1/2 h-32 w-32 -translate-y-1/2 rounded-full bg-yellow-500/10 blur-3xl"></div>
                <div class="absolute right-0 top-1/2 h-32 w-32 -translate-y-1/2 rounded-full bg-cyan-400/10 blur-3xl"></div>

                <div class="relative mx-auto max-w-[44rem]">
                    <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-4">
                        <div class="flex items-center justify-start">
                            <div
                                v-if="leftTabletFilm"
                                class="w-[13.5rem] origin-left scale-[0.82] opacity-45 blur-[1px] transition-all duration-500"
                            >
                                <MovieCard
                                    :movie="leftTabletFilm"
                                    :show-date="showDate"
                                />
                            </div>
                        </div>

                        <div class="flex items-center justify-center">
                            <div v-if="currentFilm" class="w-[15rem] transition-all duration-500">
                                <MovieCard
                                    :movie="currentFilm"
                                    :show-date="showDate"
                                />
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <div
                                v-if="rightTabletFilm"
                                class="w-[13.5rem] origin-right opacity-70 transition-all duration-500"
                                :class="{
                                    'scale-[0.82] blur-[1px]': totalFilms > 2,
                                    'scale-[0.88]': totalFilms === 2,
                                }"
                            >
                                <MovieCard
                                    :movie="rightTabletFilm"
                                    :show-date="showDate"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <button
                    type="button"
                    class="absolute left-5 top-1/2 z-30 flex h-14 w-14 -translate-y-1/2 items-center justify-center rounded-full border border-yellow-500/20 bg-yellow-500 text-slate-950 shadow-xl shadow-yellow-950/30 transition hover:scale-105 disabled:cursor-not-allowed disabled:opacity-40"
                    :disabled="!hasMultipleFilms"
                    @click="goPrev"
                >
                    <span class="text-3xl leading-none">🢀</span>
                </button>

                <button
                    type="button"
                    class="absolute right-5 top-1/2 z-30 flex h-14 w-14 -translate-y-1/2 items-center justify-center rounded-full border border-yellow-500/20 bg-yellow-500 text-slate-950 shadow-xl shadow-yellow-950/30 transition hover:scale-105 disabled:cursor-not-allowed disabled:opacity-40"
                    :disabled="!hasMultipleFilms"
                    @click="goNext"
                >
                    <span class="text-3xl leading-none">🢂</span>
                </button>
            </div>
        </div>
    </section>
</template>