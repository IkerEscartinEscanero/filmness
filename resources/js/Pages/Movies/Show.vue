<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    film: Object,
});

const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin');

const resolveStoragePath = (path) => {
    if (!path) return null;
    if (path.startsWith('/storage/')) return path;
    if (path.startsWith('storage/')) return `/${path}`;
    return `/storage/${path}`;
};

const posterUrl = computed(() => resolveStoragePath(props.film?.poster));
const trailerUrl = computed(() => resolveStoragePath(props.film?.trailer));
const logoUrl = computed(() => resolveStoragePath(props.film?.logo));

const userRating = ref(0);
const hoverRating = ref(0);

const formatDate = (date) => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
};
</script>

<template>
    <Layout>
        <Head :title="film.title" />

        <main class="min-h-screen bg-[#0F172A] text-white">

            <!-- Hero with poster background -->
            <div class="relative h-[420px] overflow-hidden">
                <img
                    v-if="posterUrl"
                    :src="posterUrl"
                    class="absolute inset-0 w-full h-full object-cover object-top"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A] via-[#0F172A]/60 to-transparent"></div>

                <!-- Content overlay on hero -->
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
                        <!-- Rating -->
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
                            @click="router.visit(`/films/${film.id}/edit`)"
                            class="px-4 py-2 rounded-xl bg-yellow-500 text-slate-950 font-semibold text-sm hover:bg-yellow-400 transition"
                        >Editar</button>
                    </div>
                </div>
            </div>

            <!-- Body of the details -->
            <div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-1 lg:grid-cols-3 gap-10">

                <!-- Left column: info + reviews -->
                <div class="lg:col-span-2 flex flex-col gap-8">
                    <!-- Film details -->
                    <div>
                        <h2 class="text-lg font-semibold text-white mb-3 flex items-center gap-2">
                            <span class="w-1 h-5 bg-yellow-500 rounded-full"></span>
                            Información detallada
                        </h2>
                        <div class="bg-slate-800/60 rounded-2xl border border-white/5 p-6">
                            <dl class="grid grid-cols-2 gap-x-8 gap-y-6">
                                <div v-if="film.release_date">
                                    <dt class="text-xs text-yellow-500 uppercase tracking-widest font-semibold mb-1">Fecha de estreno</dt>
                                    <dd class="text-slate-300 text-sm">{{ formatDate(film.release_date) }}</dd>
                                </div>
                                <div v-if="film.director">
                                    <dt class="text-xs text-yellow-500 uppercase tracking-widest font-semibold mb-1">Director</dt>
                                    <dd class="text-slate-300 text-sm">{{ film.director }}</dd>
                                </div>
                                <div v-if="film.genre">
                                    <dt class="text-xs text-yellow-500 uppercase tracking-widest font-semibold mb-1">Género</dt>
                                    <dd class="text-slate-300 text-sm">{{ film.genre }}</dd>
                                </div>
                                <div v-if="film.distribution">
                                    <dt class="text-xs text-yellow-500 uppercase tracking-widest font-semibold mb-1">Reparto</dt>
                                    <dd class="text-slate-300 text-sm">{{ film.distribution }}</dd>
                                </div>
                                <div v-if="film.synopsis" class="col-span-2">
                                    <dt class="text-xs text-yellow-500 uppercase tracking-widest font-semibold mb-1">Sinopsis</dt>
                                    <dd class="text-slate-300 text-sm leading-relaxed">{{ film.synopsis }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Showtimes (funcionalidad futura) -->
                    <div>
                        <h2 class="text-lg font-semibold text-white mb-3 flex items-center gap-2">
                            <span class="w-1 h-5 bg-yellow-500 rounded-full"></span>
                            Horarios
                        </h2>
                        <div class="bg-slate-800/60 rounded-2xl border border-white/5 p-5 text-center">
                            <p class="text-slate-500 text-sm italic">Próximamente disponible.</p>
                        </div>
                    </div>

                    <!-- Buy tickets (funcionalidad futura) -->
                    <button class="w-full py-3 rounded-2xl bg-yellow-500 text-slate-950 font-bold text-base hover:bg-yellow-400 transition shadow-lg shadow-yellow-500/20">
                        Comprar entradas
                    </button>
                </div>

                <!-- Right column: showtimes + trailer -->
                <div class="flex flex-col gap-6">
                    <!-- Trailer -->
                    <div v-if="trailerUrl">
                        <h2 class="text-lg font-semibold text-white mb-3 flex items-center gap-2">
                            <span class="w-1 h-5 bg-yellow-500 rounded-full"></span>
                            Tráiler
                        </h2>
                        <div class="rounded-2xl overflow-hidden border border-white/10 shadow-xl">
                            <video
                                :src="trailerUrl"
                                controls
                                class="w-full aspect-video object-cover"
                            />
                        </div>
                    </div>

                    <!-- Reviews -->
                    <div>
                        <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                            <span class="w-1 h-5 bg-yellow-500 rounded-full"></span>
                            Reseñas
                        </h2>
                        <!-- Reviews placeholder -->
                        <div class="flex flex-col gap-4">
                            <p class="text-slate-500 text-sm italic">Aún no hay reseñas en esta película.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </Layout>
</template>