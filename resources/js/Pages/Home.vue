<script setup>
    import Layout from '@/Layouts/Layout.vue';
    import MovieCard from '@/Components/MovieCard.vue';
    import { Head } from '@inertiajs/vue3';
    import { computed, ref } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import { router } from '@inertiajs/vue3';

    defineProps({
        movies: Array
    });

    const page = usePage();
    const authUser = computed(() => page.props.auth?.user ?? null);
    const isAdmin = computed(() => authUser.value?.role === 'admin');

    const showDeleteModal = ref(false);
    const filmToDelete = ref(null);

    const confirmDelete = (id) => {
        filmToDelete.value = id;
        showDeleteModal.value = true;
    };

    const cancelDelete = () => {
        filmToDelete.value = null;
        showDeleteModal.value = false;
    };

    const executeDelete = () => {
        router.delete(`/films/${filmToDelete.value}`);
        showDeleteModal.value = false;
        filmToDelete.value = null;
    };
</script>

<template>
    <Layout>
        <Head title="Inicio" />

        <main class="min-h-screen bg-[#0F172A] text-white">
            <div class="mx-auto max-w-7xl px-6 py-10">
                <!-- Vista para Admin -->
                <div v-if="isAdmin">
                    <div class="flex justify-between items-center mb-8">
                        <h1 class="text-3xl font-semibold text-white">Gestión de películas</h1>
                        <button class="inline-flex items-center justify-center rounded-full bg-yellow-500 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-yellow-400" @click="router.visit('/films/create')">
                            Insertar una nueva película
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-slate-800 rounded-lg overflow-hidden">
                            <thead class="bg-slate-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Título</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Director</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Género</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Fecha de Estreno</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-600">
                                <tr v-for="movie in movies" :key="movie.id" class="hover:bg-slate-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ movie.title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">{{ movie.director }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">{{ movie.genre }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">{{ movie.release_date }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-yellow-400 hover:text-yellow-300 mr-4" @click="router.visit(`/films/${movie.id}/edit`)">Editar</button>
                                        <button class="text-red-400 hover:text-red-300" @click="confirmDelete(movie.id)">Borrar</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Vista para Usuarios y Visitantes -->
                <div v-else>
                    <!-- HERO -->
                    <!-- <section class="grid gap-10 lg:grid-cols-[1.4fr_0.9fr] items-center rounded-[2rem] border border-white/10 bg-[radial-gradient(circle_at_top_left,_rgba(212,175,55,0.18),transparent_45%),_linear-gradient(180deg,#0f172a_0%,#1e293b_100%)] p-6 shadow-[0_30px_90px_-40px_rgba(0,0,0,0.8)] overflow-hidden">
                        <div class="relative min-h-[360px] overflow-hidden rounded-[1.75rem]">
                            <img
                                src="https://images.unsplash.com/photo-1517604931442-7f18f9b2d1a8?auto=format&fit=crop&w=1200&q=80"
                                alt="Cine"
                                class="h-full w-full object-cover object-center transition duration-700 hover:scale-105"
                            />
                            <div class="absolute inset-0 bg-gradient-to-r from-[#0F172A]/95 via-transparent to-[#1E293B]/95"></div>
                            <div class="absolute inset-0 bg-black/30"></div>
                        </div>

                        <div class="relative space-y-8 px-4 py-6 sm:px-8 sm:py-10">
                            <div class="inline-flex items-center gap-2 rounded-full border border-yellow-500/30 bg-yellow-500/10 px-4 py-2 text-sm text-yellow-300">
                                <span class="h-2 w-2 rounded-full bg-yellow-500 animate-pulse"></span>
                                Estrenos seleccionados
                            </div>

                            <div class="space-y-4">
                                <h1 class="text-4xl font-bold leading-tight tracking-tight text-white sm:text-5xl">
                                    Vive el cine desde la primera fila
                                </h1>
                                <p class="max-w-xl text-slate-300 sm:text-lg">
                                    Descubre películas con un diseño sofisticado, tráileres exclusivos y una experiencia visual moderna.
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-4">
                                <button class="inline-flex items-center justify-center rounded-full bg-yellow-500 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-yellow-400">
                                    Ver cartelera
                                </button>
                                <button class="inline-flex items-center justify-center rounded-full border border-slate-700 bg-slate-950/70 px-6 py-3 text-sm text-slate-100 transition hover:border-yellow-500 hover:text-yellow-500">
                                    Todos los tráileres
                                </button>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                                    <p class="text-sm uppercase tracking-[0.4em] text-slate-400">Películas</p>
                                    <p class="mt-2 text-3xl font-semibold text-white">+120</p>
                                </div>
                                <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                                    <p class="text-sm uppercase tracking-[0.4em] text-slate-400">Estrenos</p>
                                    <p class="mt-2 text-3xl font-semibold text-white">35</p>
                                </div>
                            </div>
                        </div>
                    </section> -->

                    <!-- CARTELERA -->
                    <section class="mt-14">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-sm uppercase tracking-[0.4em] text-yellow-300">Cartelera</p>
                                <h2 class="mt-2 text-3xl font-semibold text-white">Películas destacadas</h2>
                                <!-- <p class="mt-2 max-w-2xl text-slate-400">
                                    Selección curada con los mejores títulos y estrenos recientes, presentada en una cuadrícula limpia y estable.
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <button class="rounded-full border border-yellow-500/30 bg-yellow-500/10 px-4 py-2 text-sm font-medium text-yellow-200 transition hover:bg-yellow-500/20 hover:text-yellow-300">
                                    Más vistas
                                </button>
                                <button class="rounded-full border border-slate-700 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 transition hover:border-yellow-500 hover:text-yellow-500">
                                    Nuevos lanzamientos
                                </button> -->
                            </div>
                        </div>

                        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-8 justify-items-center">
                            <MovieCard
                                v-for="movie in movies"
                                :key="movie.id"
                                :movie="movie"
                            />
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </Layout>

    <!-- Modal to confirm deletion -->
    <Teleport to="body">
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 z-50 flex items-center justify-center"
        >
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="cancelDelete"></div>
            <div class="relative bg-slate-800 border border-slate-700 rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4">
                <div class="flex flex-col items-center text-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-red-500/10 flex items-center justify-center text-3xl">✘</div>
                    <h2 class="text-xl font-semibold text-white">¿Borrar película?</h2>
                    <p class="text-slate-400 text-sm">Esta acción no se puede deshacer. La película y todos sus archivos asociados serán eliminados permanentemente.</p>
                    <div class="flex gap-3 w-full mt-2">
                        <button
                            @click="cancelDelete"
                            class="flex-1 px-4 py-2 rounded-xl bg-slate-700 text-white hover:bg-slate-600 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="executeDelete"
                            class="flex-1 px-4 py-2 rounded-xl bg-red-500 text-white hover:bg-red-400 transition-colors font-semibold"
                        >
                            Borrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>