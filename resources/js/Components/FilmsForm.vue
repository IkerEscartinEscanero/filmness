<script setup>
import { useForm } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { getTrailerSource } from '@/utils/trailer';

const props = defineProps({
    film: {
        type: Object,
        default: null,
    },
    returnUrl: {
        type: String,
        default: null,
    },
});

const isEditing = !!props.film;

const form = useForm({
    title: props.film?.title ?? '',
    logo: null,
    poster: null,
    release_date: props.film?.release_date ?? '',
    director: props.film?.director ?? '',
    genre: props.film?.genre ?? '',
    distribution: props.film?.distribution ?? '',
    synopsis: props.film?.synopsis ?? '',
    duration: props.film?.duration ?? '',
    trailer_url: props.film?.trailer_url ?? '',
    return_to: props.returnUrl ?? '',
});

const formNotice = ref(null);

const logoPreview = ref(null);
const posterPreview = ref(null);

const resolveMediaPath = (path) => {
    if (!path) return null;
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    if (path.startsWith('/storage/')) return path;
    if (path.startsWith('storage/')) return `/${path}`;
    return `/storage/${path}`;
};

const trailerPreviewSource = computed(() => getTrailerSource(form.trailer_url));
const hasTrailerInput = computed(() => !!form.trailer_url?.trim());

const handleLogoChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    form.logo = file;
    logoPreview.value = URL.createObjectURL(file);
};

const handlePosterChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    form.poster = file;
    posterPreview.value = URL.createObjectURL(file);
};

const submit = () => {
    if (form.processing) return;

    formNotice.value = null;

    if (isEditing) {
        form.put(`/films/${props.film.id}`, {
            onError: () => {
                formNotice.value = 'No se ha podido actualizar la pelicula. Revisa los campos e intentalo de nuevo.';
            },
        });
    } else {
        form.post('/films', {
            onError: () => {
                formNotice.value = 'No se ha podido crear la pelicula. Revisa los campos e intentalo de nuevo.';
            },
        });
    }
};

const cancelAndGoBack = () => {
    if (props.returnUrl && props.returnUrl.startsWith('/')) {
        router.visit(props.returnUrl);
        return;
    }

    if (window.history.length > 1) {
        window.history.back();
        return;
    }

    router.visit('/admin/dashboard');
};
</script>

<template>
    <div class="max-w-2xl mx-auto bg-slate-800 p-8 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-white mb-6">
            {{ isEditing ? 'Editar película' : 'Insertar nueva película' }}
        </h1>

        <div
            v-if="formNotice"
            class="rounded-md px-4 py-3 text-sm bg-red-500/15 text-red-300 border border-red-500/30"
        >
            {{ formNotice }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-slate-300">Título *</label>
                <input
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-md text-white placeholder-slate-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                    required
                />
                <span v-if="form.errors.title" class="text-red-400 text-sm">{{ form.errors.title }}</span>
            </div>

            <div>
                <label for="logo" class="block text-sm font-medium text-slate-300 mb-1">Logo *</label>
                <label class="block cursor-pointer">
                    <input
                        id="logo"
                        type="file"
                        @change="handleLogoChange"
                        accept="image/*"
                        class="hidden"
                    />
                    <div class="border-2 border-dashed border-slate-600 rounded-lg p-4 text-center hover:border-yellow-500 transition-colors duration-200">
                        <img
                            v-if="logoPreview || (isEditing && props.film.logo)"
                            :src="logoPreview ?? resolveMediaPath(props.film.logo)"
                            class="max-h-36 mx-auto rounded mb-2 object-contain"
                        />
                        <div v-else class="py-6 text-slate-400 text-sm">Haz clic para seleccionar un logo</div>
                        <p class="text-xs text-slate-400">
                            {{ logoPreview ? 'Nueva imagen seleccionada — haz clic para cambiarla' : (isEditing && props.film.logo ? 'Haz clic para cambiar el logo' : 'Seleccionar imagen') }}
                        </p>
                    </div>
                </label>
                <span v-if="form.errors.logo" class="text-red-400 text-sm">{{ form.errors.logo }}</span>
            </div>

            <div>
                <label for="poster" class="block text-sm font-medium text-slate-300 mb-1">Póster *</label>
                <label class="block cursor-pointer">
                    <input
                        id="poster"
                        type="file"
                        @change="handlePosterChange"
                        accept="image/*"
                        class="hidden"
                    />
                    <div class="border-2 border-dashed border-slate-600 rounded-lg p-4 text-center hover:border-yellow-500 transition-colors duration-200">
                        <img
                            v-if="posterPreview || (isEditing && props.film.poster)"
                            :src="posterPreview ?? resolveMediaPath(props.film.poster)"
                            class="max-h-48 mx-auto rounded mb-2 object-contain"
                        />
                        <div v-else class="py-6 text-slate-400 text-sm">Haz clic para seleccionar un póster</div>
                        <p class="text-xs text-slate-400">
                            {{ posterPreview ? 'Nueva imagen seleccionada — haz clic para cambiarla' : (isEditing && props.film.poster ? 'Haz clic para cambiar el póster' : 'Seleccionar imagen') }}
                        </p>
                    </div>
                </label>
                <span v-if="form.errors.poster" class="text-red-400 text-sm">{{ form.errors.poster }}</span>
            </div>

            <div>
                <label for="release_date" class="block text-sm font-medium text-slate-300">Fecha de estreno *</label>
                <input
                    id="release_date"
                    v-model="form.release_date"
                    type="date"
                    class="mt-1 block w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-md text-white focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                    required
                />
                <span v-if="form.errors.release_date" class="text-red-400 text-sm">{{ form.errors.release_date }}</span>
            </div>

            <div>
                <label for="director" class="block text-sm font-medium text-slate-300">Director</label>
                <input
                    id="director"
                    v-model="form.director"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-md text-white placeholder-slate-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                />
                <span v-if="form.errors.director" class="text-red-400 text-sm">{{ form.errors.director }}</span>
            </div>

            <div>
                <label for="genre" class="block text-sm font-medium text-slate-300">Género *</label>
                <input
                    id="genre"
                    v-model="form.genre"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-md text-white placeholder-slate-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                    required
                />
                <span v-if="form.errors.genre" class="text-red-400 text-sm">{{ form.errors.genre }}</span>
            </div>

            <div>
                <label for="distribution" class="block text-sm font-medium text-slate-300">Reparto</label>
                <input
                    id="distribution"
                    v-model="form.distribution"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-md text-white placeholder-slate-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                />
                <span v-if="form.errors.distribution" class="text-red-400 text-sm">{{ form.errors.distribution }}</span>
            </div>

            <div>
                <label for="synopsis" class="block text-sm font-medium text-slate-300">Sinopsis *</label>
                <textarea
                    id="synopsis"
                    v-model="form.synopsis"
                    rows="4"
                    class="mt-1 block w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-md text-white placeholder-slate-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                    required
                ></textarea>
                <span v-if="form.errors.synopsis" class="text-red-400 text-sm">{{ form.errors.synopsis }}</span>
            </div>

            <div>
                <label for="duration" class="block text-sm font-medium text-slate-300">Duración *</label>
                <input
                    id="duration"
                    v-model="form.duration"
                    type="number"
                    class="mt-1 block w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-md text-white placeholder-slate-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                    required
                />
                <span v-if="form.errors.duration" class="text-red-400 text-sm">{{ form.errors.duration }} minutos.</span>
            </div>

            <div>
                <label for="trailer_url" class="block text-sm font-medium text-slate-300 mb-1">URL del tráiler *</label>
                <div class="space-y-3">
                    <input
                        id="trailer_url"
                        v-model="form.trailer_url"
                        type="url"
                        placeholder="https://www.youtube.com/..."
                        class="mt-1 block w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-md text-white placeholder-slate-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500"
                        required
                    />
                    <div class="border-2 border-dashed border-slate-600 rounded-lg p-4 text-center hover:border-yellow-500 transition-colors duration-200">
                        <iframe
                            v-if="trailerPreviewSource.type === 'embed'"
                            :src="trailerPreviewSource.src"
                            class="aspect-video w-full rounded mb-2"
                            title="Vista previa del trailer"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen
                        ></iframe>
                        <div v-else class="py-6 text-slate-400 text-sm">Introduce una URL de YouTube válida</div>
                        <p v-if="hasTrailerInput && trailerPreviewSource.type === 'none'" class="text-xs text-red-400">Solo se permiten enlaces de YouTube.</p>
                    </div>
                </div>
                <span v-if="form.errors.trailer_url" class="text-red-400 text-sm">{{ form.errors.trailer_url }}</span>
            </div>

            <div class="flex justify-end space-x-4">
                <button
                    type="button"
                    @click="cancelAndGoBack"
                    class="px-4 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-500"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-4 py-2 bg-yellow-500 text-slate-950 rounded-md hover:bg-yellow-400 disabled:opacity-50"
                >
                    {{ isEditing ? 'Actualizar' : 'Crear' }}
                </button>
            </div>
        </form>
    </div>
</template>