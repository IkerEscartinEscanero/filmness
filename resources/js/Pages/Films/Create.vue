<script setup>
import Layout from '@/Layouts/Layout.vue';
import FilmsForm from '@/Components/FilmsForm.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Layout>
        <Head title="Crear película" />

        <main class="min-h-screen bg-[#0F172A] text-white py-10">
            <div class="mx-auto max-w-7xl px-6">
                <FilmsForm />
            </div>
        </main>
    </Layout>
</template>