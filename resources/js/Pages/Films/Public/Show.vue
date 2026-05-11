<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { getTrailerSource } from '@/utils/trailer';
import MovieHeroHeader from '@/Components/Movies/MovieHeroHeader.vue';
import MovieSessionAdminForm from '@/Components/Movies/MovieSessionAdminForm.vue';
import DeleteReviewModal from '@/Components/DeleteReviewModal.vue';
import CreateReviewModal from '@/Components/CreateReviewModal.vue';
import UserFramedAvatar from '@/Components/UserFramedAvatar.vue';

const props = defineProps({
    film: Object,
    sessions: Array,
    rooms: Array,
    canManageSessions: Boolean,
    reviews: Array,
    averageStars: Number,
    userHasValidatedTicket: Boolean,
    userReviewId: Number,
});

const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin');
const adminSessionError = computed(() =>
    page.props.errors?.session
    || page.props.errors?.room_id
    || page.props.errors?.date
    || page.props.errors?.price
    || null
);

const resolveMediaPath = (path) => {
    if (!path) return null;
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    if (path.startsWith('/storage/')) return path;
    if (path.startsWith('storage/')) return `/${path}`;
    return `/storage/${path}`;
};

const posterUrl = computed(() => resolveMediaPath(props.film?.poster));
const trailerSource = computed(() => getTrailerSource(props.film?.trailer_url));
const logoUrl = computed(() => resolveMediaPath(props.film?.logo));
const currentUserReview = computed(() =>
    (props.reviews ?? []).find((review) => review.id === props.userReviewId) ?? null
);

const formatDate = (date) => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
};

// Group sessions by day so users can first pick a date and then a time slot
const sessionsByDay = computed(() => {
    const map = {};
    for (const s of props.sessions ?? []) {
        const d = new Date(s.date);
        const key = d.toISOString().slice(0, 10);
        if (!map[key]) map[key] = [];
        map[key].push(s);
    }
    return map;
});

const availableDays = computed(() => Object.keys(sessionsByDay.value).sort());

const dayOffset = (isoDay) => {
    const d = new Date(isoDay + 'T00:00:00');
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return Math.round((d - today) / 86400000);
};

const formatDayTab = (isoDay) => {
    const d = new Date(isoDay + 'T00:00:00');
    const diff = dayOffset(isoDay);
    if (diff === 0) return 'Hoy';
    if (diff === 1) return 'Mañana';
    return d.toLocaleDateString('es-ES', { weekday: 'long' }).toUpperCase();
};

const formatDayLong = (isoDay) =>
    new Date(isoDay + 'T00:00:00').toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'long',
    });

const formatDayDetail = (isoDay) => {
    const d = new Date(isoDay + 'T00:00:00');
    const diff = dayOffset(isoDay);

    if (diff === 0 || diff === 1) {
        return d.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long' });
    }

    return formatDayLong(isoDay);
};

const formatTime = (iso) =>
    new Date(iso).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });

const selectedDay = ref(availableDays.value[0] ?? null);
const selectedSession = ref(null);

const showReviewModal = ref(false);
const showDeleteReviewModal = ref(false);
const reviewIdToDelete = ref(null);
const reviewForm = useForm({
    stars: 0,
    comment: '',
});

const sessionsForDay = computed(() =>
    selectedDay.value ? (sessionsByDay.value[selectedDay.value] ?? []) : []
);

function selectDay(day) {
    selectedDay.value = day;
    selectedSession.value = null;
}

function selectSession(session) {
    selectedSession.value = session;
}

function goToSeats() {
    if (!selectedSession.value) return;
    router.visit(`/sessions/${selectedSession.value.id}/seats`);
}

function deleteSession(sessionId) {
    router.delete(`/sessions/${sessionId}`, { preserveScroll: true });
}

function openReviewModal() {
    showReviewModal.value = true;
    reviewForm.reset();
}

function closeReviewModal() {
    showReviewModal.value = false;
    reviewForm.reset();
}

function submitReview() {
    reviewForm.post(route('films.reviews.store', props.film.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeReviewModal();
        },
    });
}

function requestDeleteReview(reviewId) {
    reviewIdToDelete.value = reviewId;
    showDeleteReviewModal.value = true;
}

function closeDeleteReviewModal() {
    showDeleteReviewModal.value = false;
    reviewIdToDelete.value = null;
}

function confirmDeleteReview() {
    if (!reviewIdToDelete.value) return;

    router.delete(route('reviews.destroy', reviewIdToDelete.value), {
        preserveScroll: true,
        onFinish: () => {
            closeDeleteReviewModal();
        },
    });
}

const starsLabel = (stars) => '★'.repeat(stars) + '☆'.repeat(Math.max(0, 5 - stars));
</script>

<template>
    <Layout>
        <Head :title="film.title" />

        <main class="min-h-screen bg-[#0F172A] text-white">
            <MovieHeroHeader
                :film="film"
                :poster-url="posterUrl"
                :logo-url="logoUrl"
                :is-admin="isAdmin"
                :average-stars="averageStars ?? null"
                @edit="router.visit(`/films/${film.id}/edit`)"
            />

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

                    <!-- Showtimes -->
                    <div>
                        <h2 class="text-lg font-semibold text-white mb-3 flex items-center gap-2">
                            <span class="w-1 h-5 bg-yellow-500 rounded-full"></span>
                            Horarios
                        </h2>

                        <MovieSessionAdminForm
                            v-if="canManageSessions"
                            :film-id="film.id"
                            :rooms="rooms"
                            :error-message="adminSessionError"
                        />

                        <div v-if="availableDays.length === 0" class="bg-slate-800/60 rounded-2xl border border-white/5 p-5 text-center">
                            <p class="text-slate-500 text-sm italic">No hay sesiones disponibles próximamente.</p>
                        </div>

                        <div v-else class="bg-slate-800/60 rounded-2xl border border-white/5 p-5 flex flex-col gap-5">
                            <div class="rounded-xl border border-white/5 bg-slate-900/40 p-3">
                                <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Elige día</p>
                                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                    <button
                                        v-for="day in availableDays"
                                        :key="day"
                                        @click="selectDay(day)"
                                        class="cursor-pointer rounded-xl border px-3 py-3 text-left transition"
                                        :class="selectedDay === day
                                            ? 'bg-yellow-500/15 border-yellow-500 text-yellow-300'
                                            : 'bg-slate-800 border-slate-700 text-slate-300 hover:border-slate-500'"
                                    >
                                        <p class="text-xs uppercase tracking-widest opacity-80">{{ formatDayTab(day) }}</p>
                                        <p class="text-sm font-semibold mt-1">{{ formatDayDetail(day) }}</p>
                                    </button>
                                </div>
                            </div>

                            <div class="rounded-xl border border-white/5 bg-slate-900/40 p-3">
                                <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Elige hora</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <div
                                        v-for="session in sessionsForDay"
                                        :key="session.id"
                                        class="flex items-center gap-1"
                                    >
                                        <button
                                            @click="selectSession(session)"
                                            class="cursor-pointer px-4 py-2.5 rounded-xl text-sm font-semibold transition border min-w-[120px]"
                                            :class="selectedSession?.id === session.id
                                                ? 'bg-yellow-500 text-slate-950 border-yellow-500 shadow-md shadow-yellow-500/20'
                                                : 'bg-slate-800 text-slate-100 border-slate-600 hover:border-yellow-500/60 hover:text-yellow-400'"
                                        >
                                            <span class="text-base">{{ formatTime(session.date) }}</span>
                                            <span class="block text-[11px] font-normal opacity-70 mt-0.5">{{ session.room }}</span>
                                        </button>

                                        <button
                                            v-if="canManageSessions"
                                            @click="deleteSession(session.id)"
                                            class="h-9 w-9 rounded-lg border border-red-500/40 text-red-400 hover:bg-red-500/20 transition"
                                            title="Eliminar horario"
                                        >
                                            X
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buy tickets -->
                    <button
                        @click="goToSeats"
                        :disabled="!selectedSession"
                        class="w-full py-3 rounded-2xl font-bold text-base transition shadow-lg"
                        :class="selectedSession
                            ? 'bg-yellow-500 text-slate-950 hover:bg-yellow-400 shadow-yellow-500/20 cursor-pointer'
                            : 'bg-slate-700 text-slate-500 cursor-not-allowed'"
                    >
                        Comprar entradas
                    </button>
                </div>

                <!-- Right column: showtimes + trailer -->
                <div class="flex flex-col gap-6">
                    <!-- Trailer -->
                    <div v-if="trailerSource.type !== 'none'">
                        <h2 class="text-lg font-semibold text-white mb-3 flex items-center gap-2">
                            <span class="w-1 h-5 bg-yellow-500 rounded-full"></span>
                            Tráiler
                        </h2>
                        <div class="rounded-2xl overflow-hidden border border-white/10 shadow-xl">
                            <iframe
                                v-if="trailerSource.type === 'embed'"
                                :src="trailerSource.src"
                                class="w-full aspect-video"
                                title="Trailer"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allowfullscreen
                            ></iframe>
                        </div>
                    </div>

                    <!-- Reviews -->
                    <div>
                        <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                            <span class="w-1 h-5 bg-yellow-500 rounded-full"></span>
                            Reseñas
                        </h2>

                        <button
                            v-if="userHasValidatedTicket && !userReviewId"
                            @click="openReviewModal"
                            class="mb-4 w-full py-2 rounded-xl bg-yellow-500 text-slate-950 font-semibold hover:bg-yellow-400 transition"
                        >
                            Dejar reseña
                        </button>

                        <div v-if="currentUserReview" class="mb-4 rounded-xl border border-yellow-500/30 bg-yellow-500/10 p-4">
                            <div class="mb-3 flex items-start justify-between gap-3">
                                <div class="flex gap-3">
                                    <UserFramedAvatar
                                        :src="currentUserReview.userAvatar"
                                        :alt="currentUserReview.userName"
                                        size-class="h-11 w-11"
                                        inner-inset-class="inset-[0.38rem]"
                                    />
                                    <div>
                                        <p class="text-sm font-semibold text-yellow-300">{{ currentUserReview.userName || 'Tu reseña' }}</p>
                                        <p class="text-xs text-slate-400">{{ currentUserReview.date }}</p>
                                    </div>
                                </div>
                                <button
                                    @click="requestDeleteReview(userReviewId)"
                                    class="text-xs px-2 py-1 rounded-lg bg-red-500/20 text-red-300 hover:bg-red-500/30 transition"
                                >
                                    Eliminar
                                </button>
                            </div>
                            <div class="space-y-2">
                                <p class="text-sm text-yellow-300">{{ starsLabel(currentUserReview.stars) }}</p>
                                <p class="text-sm text-slate-300">{{ currentUserReview.comment || 'Sin comentario' }}</p>
                            </div>
                        </div>

                        <div v-if="reviews.length" class="flex flex-col gap-3">
                            <article
                                v-for="review in reviews"
                                :key="review.id"
                                v-show="review.id !== userReviewId"
                                class="rounded-xl bg-slate-800/60 border border-white/5 p-4"
                            >
                                <div class="flex gap-3 mb-3">
                                    <UserFramedAvatar
                                        :src="review.userAvatar"
                                        :alt="review.userName"
                                        size-class="h-11 w-11"
                                        inner-inset-class="inset-[0.38rem]"
                                    />
                                    <div>
                                        <p class="font-semibold text-white text-sm">{{ review.userName }}</p>
                                        <p class="text-xs text-slate-400">{{ review.date }}</p>
                                    </div>
                                </div>
                                <p class="text-yellow-300 text-sm mb-2">{{ starsLabel(review.stars) }}</p>
                                <p class="text-slate-300 text-sm">{{ review.comment || 'Sin comentario' }}</p>
                            </article>
                        </div>

                        <p v-else class="text-slate-500 text-sm italic">Aún no hay reseñas en esta película.</p>
                    </div>
                </div>
            </div>
        </main>

        <CreateReviewModal
            :show="showReviewModal"
            :stars="reviewForm.stars"
            :comment="reviewForm.comment"
            :processing="reviewForm.processing"
            :errors="reviewForm.errors"
            @close="closeReviewModal"
            @submit="submitReview"
            @update:stars="reviewForm.stars = $event"
            @update:comment="reviewForm.comment = $event"
        />

        <DeleteReviewModal
            :show="showDeleteReviewModal"
            @cancel="closeDeleteReviewModal"
            @confirm="confirmDeleteReview"
        />
    </Layout>
</template>