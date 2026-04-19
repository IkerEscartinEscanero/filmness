<script setup>
    import Layout from '@/Layouts/Layout.vue';
    import FilmsSection from '@/Components/FilmsSection.vue';
    import DeleteFilmModal from '@/Components/DeleteFilmModal.vue';
    import { Head, usePage, router } from '@inertiajs/vue3';
    import { computed, ref } from 'vue';

    defineProps({
        films: Array,
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
        <Head title="Cartelera" />

        <main class="min-h-screen bg-[#0F172A] text-white">
            <div class="mx-auto max-w-7xl px-6 py-10">
                <!-- Admin: management table -->
                <div v-if="isAdmin" class="mb-16">
                    <div class="flex justify-between items-center mb-8">
                        <h1 class="text-3xl font-semibold text-white">Gestión de cartelera</h1>
                        <button
                            class="inline-flex items-center justify-center rounded-full bg-yellow-500 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-yellow-400"
                            @click="router.visit('/films/create')"
                        >
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
                                <tr v-for="film in films" :key="film.id" class="hover:bg-slate-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ film.title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">{{ film.director }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">{{ film.genre }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">{{ film.release_date }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-yellow-400 hover:text-yellow-300 mr-4" @click="router.visit(`/films/${film.id}/edit`)">Editar</button>
                                        <button class="text-red-400 hover:text-red-300" @click="confirmDelete(film.id)">Borrar</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <section v-else>
                    <!-- This shared section avoids repeating the public grid layout. -->
                    <FilmsSection
                        eyebrow="En cine"
                        title="Cartelera"
                        :films="films"
                        empty-message="No hay películas en cartelera actualmente."
                        section-class=""
                    />
                </section>
            </div>
        </main>
    </Layout>

    <!-- Modal to confirm deletion -->
    <DeleteFilmModal
        :show="showDeleteModal"
        @cancel="cancelDelete"
        @confirm="executeDelete"
    />
</template>
