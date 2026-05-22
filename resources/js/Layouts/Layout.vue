<script setup>
    import Header from '@/Layouts/Header.vue';
    import Footer from '@/Layouts/Footer.vue';
</script>

<template>
    <div class="min-h-screen fn-bg fn-text">
        <Header />
        <slot />
        <Footer />
    </div>
</template>
