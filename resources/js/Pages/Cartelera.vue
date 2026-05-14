<script setup>
    import Layout from '@/Layouts/Layout.vue';
    import FilmsSection from '@/Components/FilmsSection.vue';
    import { Head } from '@inertiajs/vue3';

    defineProps({
        films: Array,
    });
</script>

<template>
    <Layout>
        <Head title="Cartelera" />

        <main class="min-h-screen bg-[#0F172A] text-white">
            <div class="mx-auto max-w-7xl px-6 py-10">
                <section>
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
</template>
