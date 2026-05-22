<script setup>
    import Layout from '@/Layouts/Layout.vue';
    import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Layout>
        <Head title="Sobre nosotros" />

        <main class="min-h-screen bg-[#0F172A] text-white">
            <section class="about-hero relative overflow-hidden border-b border-white/10 bg-slate-900/60">
                <div class="about-hero-overlay absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(234,179,8,0.16),_transparent_35%),linear-gradient(180deg,_rgba(15,23,42,0.55)_0%,_rgba(15,23,42,0.95)_100%)]"></div>
                <div class="relative mx-auto flex max-w-6xl flex-col items-center px-6 py-20 text-center md:py-28">
                    <p class="text-sm uppercase tracking-[0.5em] text-yellow-300">Sobre nosotros</p>
                    <h1 class="about-hero-title mt-6 text-5xl font-bold tracking-[0.18em] text-white md:text-7xl">FILMNESS</h1>
                    <p class="about-hero-copy mt-6 max-w-2xl text-lg text-slate-300 md:text-xl">
                        Tu punto de encuentro con el cine.
                    </p>
                    <p class="about-hero-copy mt-4 max-w-3xl text-sm leading-7 text-slate-400 md:text-base">
                        Una plataforma creada para descubrir películas, seguir próximos estrenos y consultar todo de manera rápida, visual y sencilla.
                    </p>
                </div>
            </section>

            <section class="mx-auto max-w-6xl px-6 py-14 md:py-20">
                <div class="mx-auto max-w-3xl text-center">
                    <h2 class="text-2xl font-semibold text-white md:text-3xl">Un poco sobre FilmNess</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-400 md:text-base">
                        Queremos que la experiencia empiece antes de entrar en la sala, con una web donde todo sea fácil de encontrar y agradable de consultar.
                    </p>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    <article class="about-card rounded-3xl border border-white/10 bg-slate-900/70 p-6">
                        <h3 class="text-lg font-semibold text-white">¿Qué es FilmNess?</h3>
                        <p class="mt-4 text-sm leading-7 text-slate-300">
                            FilmNess es una plataforma pensada para reunir en un mismo lugar la cartelera, los próximos estrenos y toda la información necesaria para disfrutar del cine de una forma más sencilla.
                        </p>
                    </article>

                    <article class="about-card rounded-3xl border border-white/10 bg-slate-900/70 p-6">
                        <h3 class="text-lg font-semibold text-white">¿A qué nos dedicamos?</h3>
                        <p class="mt-4 text-sm leading-7 text-slate-300">
                            Nos dedicamos a ofrecer una experiencia cómoda y clara para que cualquier usuario pueda descubrir películas, consultar información útil y preparar su próxima salida al cine sin complicaciones.
                        </p>
                    </article>

                    <article class="about-card rounded-3xl border border-white/10 bg-slate-900/70 p-6">
                        <h3 class="text-lg font-semibold text-white">Nuestro objetivo</h3>
                        <p class="mt-4 text-sm leading-7 text-slate-300">
                            Queremos ofrecer a nuestros clientes una web práctica, visual y fácil de usar, donde encontrar lo importante rápidamente y vivir una experiencia más completa desde el primer momento.
                        </p>
                    </article>
                </div>
            </section>
        </main>
    </Layout>
</template>