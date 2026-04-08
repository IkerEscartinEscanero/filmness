<script setup>
import Layout from '@/Layouts/Layout.vue';
import FilmsForm from '@/Components/FilmsForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    film: Object,
});
</script>

<template>
    <Layout>
        <Head title="Editar película" />

        <main class="min-h-screen bg-[#0F172A] text-white py-10">
            <div class="mx-auto max-w-7xl px-6">
                <FilmsForm :film="film" />
            </div>
        </main>
    </Layout>
</template>