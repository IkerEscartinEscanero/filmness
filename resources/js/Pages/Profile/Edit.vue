<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import DeleteReviewModal from '@/Components/DeleteReviewModal.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import Layout from '@/Layouts/Layout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref, watch } from 'vue';

const props = defineProps({
    status: {
        type: String,
        default: null,
    },
    profile: {
        type: Object,
        required: true,
    },
    watchedFilms: {
        type: Array,
        default: () => [],
    },
    reviews: {
        type: Array,
        default: () => [],
    },
    discounts: {
        type: Array,
        default: () => [],
    },
});

const showEditModal = ref(false);
const showDeleteReviewModal = ref(false);
const confirmProfileChanges = ref(false);
const localAvatarPreview = ref(null);
const dismissProfileUpdate = ref(false);
const reviewIdToDelete = ref(null);
let profileUpdateTimeout = null;

const profileForm = useForm({
    name: props.profile.name ?? '',
    email: props.profile.email ?? '',
    birth_date: props.profile.birth_date ?? '',
    avatar: null,
    current_password: '',
    password: '',
    password_confirmation: '',
});

const resolveMediaPath = (path) => {
    if (!path) return null;
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    if (path.startsWith('/')) return path;
    if (path.startsWith('storage/')) return `/${path}`;
    return `/storage/${path}`;
};

const starsLabel = (stars) => '★'.repeat(stars) + '☆'.repeat(Math.max(0, 5 - stars));

const profileAvatarUrl = computed(() => resolveMediaPath(props.profile.avatar_url));
const modalAvatarPreview = computed(() => localAvatarPreview.value || profileAvatarUrl.value);
const displayBirthDate = computed(() => {
    if (!props.profile.birth_date) {
        return 'No indicado';
    }

    const [year, month, day] = props.profile.birth_date.split('-');
    if (!year || !month || !day) {
        return props.profile.birth_date;
    }

    return `${day}/${month}/${year}`;
});

const openEditModal = () => {
    showEditModal.value = true;
    confirmProfileChanges.value = false;
};

const clearLocalAvatarPreview = () => {
    if (!localAvatarPreview.value) return;
    URL.revokeObjectURL(localAvatarPreview.value);
    localAvatarPreview.value = null;
};

const resetSensitiveProfileFields = () => {
    profileForm.reset('current_password', 'password', 'password_confirmation', 'avatar');
};

const closeEditModal = () => {
    showEditModal.value = false;
    confirmProfileChanges.value = false;
    profileForm.clearErrors();
    resetSensitiveProfileFields();
    clearLocalAvatarPreview();
};

const onAvatarChange = (event) => {
    const file = event.target.files?.[0] ?? null;
    profileForm.avatar = file;

    clearLocalAvatarPreview();

    if (!file) {
        return;
    }

    localAvatarPreview.value = URL.createObjectURL(file);
};

const requestSave = () => {
    confirmProfileChanges.value = true;
};

const saveProfile = () => {
    profileForm.patch(route('profile.update'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            closeEditModal();
        },
    });
};

const requestDeleteReview = (reviewId) => {
    reviewIdToDelete.value = reviewId;
    showDeleteReviewModal.value = true;
};

const closeDeleteReviewModal = () => {
    showDeleteReviewModal.value = false;
    reviewIdToDelete.value = null;
};

const confirmDeleteReview = () => {
    if (!reviewIdToDelete.value) return;

    router.delete(route('reviews.destroy', reviewIdToDelete.value), {
        preserveScroll: true,
        onFinish: () => {
            closeDeleteReviewModal();
        },
    });
};

watch(() => props.status, (value) => {
    dismissProfileUpdate.value = false;
    if (value === 'profile-updated') {
        if (profileUpdateTimeout) {
            clearTimeout(profileUpdateTimeout);
        }
        profileUpdateTimeout = window.setTimeout(() => {
            dismissProfileUpdate.value = true;
        }, 5500);
    }
});

onBeforeUnmount(() => {
    if (profileUpdateTimeout) {
        clearTimeout(profileUpdateTimeout);
    }
    clearLocalAvatarPreview();
});
</script>

<template>
    <Head title="Mi perfil" />

    <Layout>
        <main class="min-h-screen bg-[#0F172A] text-white">
            <div class="mx-auto max-w-7xl px-6 py-10 space-y-8">
                <div v-if="status === 'profile-updated' && !dismissProfileUpdate" class="rounded-2xl border border-emerald-400/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    Perfil actualizado correctamente.
                </div>

                <section class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70 p-6 md:p-8">
                    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(234,179,8,0.14),transparent_30%),radial-gradient(circle_at_bottom_right,rgba(56,189,248,0.08),transparent_30%)]"></div>
                    <div class="relative">
                        <div>
                            <div>
                                <h2 class="text-xs font-semibold md:text-2xl uppercase tracking-[0.35em] text-yellow-300/80">Mi cuenta</h2>
                                <p class="mt-2 max-w-2xl text-sm text-slate-400 md:text-base">Tu espacio personal para gestionar la cuenta, la imagen de perfil y los datos principales.</p>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col gap-8 lg:flex-row lg:items-center lg:gap-12">
                            <div class="flex shrink-0 justify-center lg:justify-start">
                                <div class="relative h-[15.5rem] w-[15.5rem] overflow-visible sm:h-[17rem] sm:w-[17rem]">
                                    <div class="absolute inset-[1.35rem] z-10 overflow-hidden rounded-full border border-white/15 bg-slate-800 shadow-[0_24px_60px_-24px_rgba(15,23,42,0.95)]">
                                        <img :src="profileAvatarUrl" alt="Foto de perfil" class="h-full w-full rounded-full object-cover" />
                                    </div>
                                    <div class="absolute inset-0 rounded-full bg-yellow-500/10 blur-2xl"></div>
                                    <img
                                        src="/images/Render1.png"
                                        alt="Marco de palomitas"
                                        class="pointer-events-none absolute left-1/2 top-1/2 z-20 h-full w-full -translate-x-1/2 -translate-y-1/2 object-contain drop-shadow-[0_14px_28px_rgba(15,23,42,0.45)]"
                                    />
                                </div>
                            </div>

                            <div class="min-w-0 flex-1 rounded-[1.75rem] border border-white/10 bg-slate-800/75 p-6 shadow-[inset_0_1px_0_rgba(255,255,255,0.04)]">
                                <div class="space-y-5">
                                    <div class="border-b border-white/8 pb-4">
                                        <p class="text-xs uppercase tracking-[0.28em] text-slate-500">Nombre</p>
                                        <p class="mt-2 text-lg font-semibold text-white">{{ profile.name }}</p>
                                    </div>

                                    <div class="border-b border-white/8 pb-4">
                                        <p class="text-xs uppercase tracking-[0.28em] text-slate-500">Email</p>
                                        <p class="mt-2 break-all text-lg font-semibold text-white">{{ profile.email }}</p>
                                    </div>

                                    <div class="border-b border-white/8 pb-4">
                                        <p class="text-xs uppercase tracking-[0.28em] text-slate-500">Contraseña</p>
                                        <p class="mt-2 font-medium tracking-[0.4em] text-slate-100">••••••••••••</p>
                                    </div>

                                    <div>
                                        <p class="text-xs uppercase tracking-[0.28em] text-slate-500">Cumpleaños</p>
                                        <p class="mt-2 text-lg font-semibold text-white">{{ displayBirthDate }}</p>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <button type="button" class="rounded-full bg-yellow-500 px-5 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-yellow-400" @click="openEditModal">
                                        Modificar perfil
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70 p-6 md:p-8">
                    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(234,179,8,0.14),transparent_30%),radial-gradient(circle_at_bottom_right,rgba(56,189,248,0.08),transparent_30%)]"></div>
                    <div class="relative">
                        <h2 class="text-xs font-semibold md:text-2xl uppercase tracking-[0.35em] text-yellow-300/80">Mis películas vistas</h2>

                        <div v-if="watchedFilms.length" class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-5">
                        <article v-for="film in watchedFilms" :key="film.id" class="overflow-hidden rounded-2xl border border-white/10 bg-slate-800/70">
                            <img v-if="film.poster" :src="resolveMediaPath(film.poster)" :alt="film.title" class="h-65 w-full object-cover" />
                            <div v-else class="flex h-44 items-center justify-center bg-slate-700 text-4xl">🎬</div>
                            <div class="p-3">
                                <p class="line-clamp-2 text-sm font-semibold">{{ film.title }}</p>
                                <p class="mt-1 text-xs text-slate-400">Vista en: {{ film.watchedAt }}</p>
                                <div class="mt-3">
                                    <button
                                        v-if="film.reviewId"
                                        @click="requestDeleteReview(film.reviewId)"
                                        class="w-full px-2 py-1.5 rounded-lg bg-red-500/20 text-red-300 hover:bg-red-500/30 transition text-xs font-semibold"
                                    >
                                        Borrar reseña
                                    </button>
                                    <Link
                                        v-else
                                        :href="`/movies/${film.id}`"
                                        class="block w-full px-2 py-1.5 rounded-lg bg-yellow-500/20 text-yellow-300 hover:bg-yellow-500/30 transition text-xs font-semibold text-center"
                                    >
                                        Reseñar película
                                    </Link>
                                </div>
                            </div>
                        </article>
                        </div>

                        <p v-else class="mt-5 rounded-2xl border border-dashed border-white/20 bg-slate-800/40 p-4 text-sm text-slate-400">
                            ¡Es hora de ir a ver alguna película!
                        </p>
                    </div>
                </section>

                <section class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70 p-6 md:p-8">
                    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(234,179,8,0.14),transparent_30%),radial-gradient(circle_at_bottom_right,rgba(56,189,248,0.08),transparent_30%)]"></div>
                    <div class="relative">
                        <h2 class="text-xs font-semibold md:text-2xl uppercase tracking-[0.35em] text-yellow-300/80">Mis reseñas</h2>

                        <div v-if="reviews.length" class="mt-6 space-y-4">
                        <article v-for="review in reviews" :key="review.id" class="rounded-2xl border border-white/10 bg-slate-800/70 p-4">
                            <div class="flex items-start gap-4">
                                <div class="h-12 w-12 shrink-0 overflow-hidden rounded-full border border-white/20 bg-slate-900">
                                    <img v-if="review.filmLogo" :src="resolveMediaPath(review.filmLogo)" :alt="review.filmTitle" class="h-full w-full object-cover" />
                                    <div v-else class="flex h-full w-full items-center justify-center">🎞️</div>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                                        <p class="font-semibold">{{ review.filmTitle }}</p>
                                        <p class="text-xs text-slate-400">{{ review.date }}</p>
                                        <p class="text-sm text-yellow-300">{{ starsLabel(review.stars) }}</p>
                                    </div>
                                    <p class="mt-2 text-sm text-slate-300">{{ review.comment || 'Sin comentario' }}</p>
                                </div>
                            </div>
                        </article>
                        </div>

                        <p v-else class="mt-5 rounded-2xl border border-dashed border-white/20 bg-slate-800/40 p-4 text-sm text-slate-400">
                            ¿Has visto alguna película? ¡Es hora de dejar una reseña!
                        </p>
                    </div>
                </section>

                <section class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70 p-6 md:p-8">
                    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(234,179,8,0.14),transparent_30%),radial-gradient(circle_at_bottom_right,rgba(56,189,248,0.08),transparent_30%)]"></div>
                    <div class="relative">
                        <h2 class="text-xs font-semibold md:text-2xl uppercase tracking-[0.35em] text-yellow-300/80">Mis descuentos</h2>

                        <div v-if="discounts.length" class="mt-6 grid grid-cols-1 gap-3 md:grid-cols-2">
                        <article
                            v-for="discount in discounts"
                            :key="discount.id"
                            class="rounded-2xl border px-4 py-3"
                            :class="discount.active
                                ? 'border-emerald-400/30 bg-emerald-500/10'
                                : 'border-slate-600/80 bg-slate-800/50 opacity-70'"
                        >
                            <div class="flex items-center justify-between gap-3">
                                <p class="font-medium">{{ discount.label }}</p>
                                <span class="rounded-full px-2 py-1 text-xs font-semibold"
                                      :class="discount.active ? 'bg-emerald-400/15 text-emerald-200' : 'bg-slate-700 text-slate-300'">
                                    {{ discount.active ? 'Disponible' : 'Bloqueado/Gastado' }}
                                </span>
                            </div>
                            <p class="mt-2 text-sm text-yellow-300">{{ discount.value }}</p>
                            <p v-if="discount.expiration_date" class="mt-1 text-xs text-slate-400">Caduca: {{ discount.expiration_date }}</p>
                        </article>
                        </div>

                        <p v-else class="mt-5 rounded-2xl border border-dashed border-white/20 bg-slate-800/40 p-4 text-sm text-slate-400">
                            No hay descuentos configurados para este usuario.
                        </p>
                    </div>
                </section>
            </div>
        </main>

        <Modal :show="showEditModal" max-width="2xl" @close="closeEditModal">
            <div class="bg-slate-900 p-6 text-white">
                <h3 class="text-xl font-semibold text-yellow-300">Modificar perfil</h3>
                <p class="mt-2 text-sm text-slate-400">Puedes cambiar datos personales, foto de perfil y contraseña.</p>

                <form class="mt-6 space-y-4" @submit.prevent="requestSave">
                    <div>
                        <InputLabel for="edit_name" value="Nombre" class="text-slate-200" />
                        <TextInput id="edit_name" v-model="profileForm.name" type="text" class="mt-1 block w-full bg-slate-800 border-slate-600 text-white" required />
                        <InputError class="mt-2" :message="profileForm.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="edit_email" value="Email" class="text-slate-200" />
                        <TextInput id="edit_email" v-model="profileForm.email" type="email" class="mt-1 block w-full bg-slate-800 border-slate-600 text-white" required />
                        <InputError class="mt-2" :message="profileForm.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="edit_birth_date" value="Fecha de cumpleaños" class="text-slate-200" />
                        <TextInput id="edit_birth_date" v-model="profileForm.birth_date" type="date" class="mt-1 block w-full bg-slate-800 border-slate-600 text-white" />
                        <InputError class="mt-2" :message="profileForm.errors.birth_date" />
                    </div>

                    <div>
                        <InputLabel for="edit_avatar" value="Foto de perfil" class="text-slate-200" />
                        <label for="edit_avatar" class="mt-1 block cursor-pointer">
                            <input
                                id="edit_avatar"
                                type="file"
                                accept="image/png,image/jpeg,image/jpg,image/webp"
                                class="hidden"
                                @change="onAvatarChange"
                            />

                            <div class="rounded-2xl border-2 border-dashed border-slate-600 bg-slate-800/70 p-4 text-center transition-colors duration-200 hover:border-yellow-500">
                                <div class="mx-auto flex w-full max-w-[12rem] justify-center">
                                    <div class="relative h-40 w-40 overflow-visible">
                                        <div class="absolute inset-[1rem] z-10 overflow-hidden rounded-full bg-slate-900 shadow-lg shadow-slate-950/50">
                                            <img
                                                v-if="modalAvatarPreview"
                                                :src="modalAvatarPreview"
                                                alt="Vista previa del avatar"
                                                class="h-full w-full rounded-full object-cover"
                                            />
                                            <div v-else class="flex h-full w-full items-center justify-center rounded-full bg-slate-700 text-4xl">
                                                👤
                                            </div>
                                        </div>

                                        <img
                                            src="/images/Render1.png"
                                            alt="Marco de palomitas"
                                            class="pointer-events-none absolute left-1/2 top-1/2 z-20 h-full w-full -translate-x-1/2 -translate-y-1/2 object-contain"
                                        />
                                    </div>
                                </div>

                                <p class="mt-3 text-sm text-slate-200">
                                    {{ localAvatarPreview ? 'Nueva foto seleccionada' : 'Haz clic para seleccionar una foto de perfil' }}
                                </p>
                            </div>
                        </label>
                        <InputError class="mt-2" :message="profileForm.errors.avatar" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <InputLabel for="edit_current_password" value="Contraseña actual" class="text-slate-200" />
                            <TextInput id="edit_current_password" v-model="profileForm.current_password" type="password" class="mt-1 block w-full bg-slate-800 border-slate-600 text-white" autocomplete="current-password" />
                            <InputError class="mt-2" :message="profileForm.errors.current_password" />
                        </div>

                        <div>
                            <InputLabel for="edit_password" value="Nueva contraseña" class="text-slate-200" />
                            <TextInput id="edit_password" v-model="profileForm.password" type="password" class="mt-1 block w-full bg-slate-800 border-slate-600 text-white" autocomplete="new-password" />
                            <InputError class="mt-2" :message="profileForm.errors.password" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="edit_password_confirmation" value="Confirmar nueva contraseña" class="text-slate-200" />
                        <TextInput id="edit_password_confirmation" v-model="profileForm.password_confirmation" type="password" class="mt-1 block w-full bg-slate-800 border-slate-600 text-white" autocomplete="new-password" />
                        <InputError class="mt-2" :message="profileForm.errors.password_confirmation" />
                    </div>

                    <div v-if="confirmProfileChanges" class="rounded-xl border border-yellow-500/40 bg-yellow-500/10 px-4 py-3 text-sm text-yellow-100">
                        Confirma que quieres aplicar estos cambios en tu perfil.
                    </div>

                    <div class="mt-2 flex items-center justify-end gap-3">
                        <button type="button" class="rounded-full border border-white/20 px-4 py-2 text-sm hover:bg-white/5" @click="closeEditModal">
                            Cancelar
                        </button>

                        <button v-if="!confirmProfileChanges" type="submit" class="rounded-full bg-yellow-500 px-5 py-2 text-sm font-semibold text-slate-900 hover:bg-yellow-400">
                            Revisar cambios
                        </button>

                        <button v-else type="button" class="rounded-full bg-emerald-500 px-5 py-2 text-sm font-semibold text-slate-950 hover:bg-emerald-400" :disabled="profileForm.processing" @click="saveProfile">
                            Confirmar y guardar
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <DeleteReviewModal
            :show="showDeleteReviewModal"
            @cancel="closeDeleteReviewModal"
            @confirm="confirmDeleteReview"
        />
    </Layout>
</template>