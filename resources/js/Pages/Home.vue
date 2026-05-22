<script setup>
    import Layout from '@/Layouts/Layout.vue';
    import FilmsSection from '@/Components/FilmsSection.vue';
    import { Head } from '@inertiajs/vue3';

    defineProps({
        upcomingFilms: Array,
        billboardFilms: Array,
    });
</script>

<template>
    <Layout>
        <Head title="Inicio" />

        <main class="min-h-screen bg-[#0F172A] text-white">
            <div class="mx-auto max-w-7xl px-6 py-10">
                <!-- Reusable section for the public film blocks shown on Home -->
                <FilmsSection
                    eyebrow="En cine"
                    title="Cartelera"
                    :films="billboardFilms"
                    empty-message="No hay películas en cartelera actualmente."
                    section-class=""
                    :use-carousel="true"
                />

                <FilmsSection
                    eyebrow="Próximamente"
                    title="Próximos estrenos"
                    :films="upcomingFilms"
                    empty-message="No hay próximos estrenos disponibles por el momento."
                    :show-date="true"
                    :use-carousel="true"
                />

                <!-- Discounts section -->
                <section class="mt-16">
                    <p class="text-sm uppercase tracking-[0.4em] text-yellow-300">Ahorra más</p>
                    <h2 class="mt-2 text-3xl font-semibold text-white">Descuentos disponibles</h2>

                    <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-6">

                        <!-- Register discount -->
                        <div class="flex flex-col gap-4 rounded-2xl border border-white/10 bg-slate-800/60 p-7 hover:border-yellow-500/40 transition">
                            <div class="discount-icon-badge w-12 h-12 rounded-full flex items-center justify-center text-2xl"><img src="/images/ticket.png" class="w-7 h-7 object-contain"/></div>
                            <h3 class="text-lg font-semibold text-white">Regístrate y ahorra</h3>
                            <p class="text-slate-400 text-sm leading-relaxed">
                                Crea tu cuenta en Filmness y obtén un descuento de bienvenida en tu primera compra. ¡Solo por unirte a nuestra comunidad!
                            </p>
                            <span class="mt-auto inline-block rounded-full bg-yellow-500/10 border border-yellow-500/30 px-4 py-1 text-xs font-semibold text-yellow-300 w-fit">Descuento del 10% por registro</span>
                        </div>

                        <!-- Birthday discount -->
                        <div class="flex flex-col gap-4 rounded-2xl border border-white/10 bg-slate-800/60 p-7 hover:border-yellow-500/40 transition">
                            <div class="discount-icon-badge w-12 h-12 rounded-full flex items-center justify-center text-2xl"><img src="/images/tarta.png" class="w-7 h-7 object-contain"/></div>
                            <h3 class="text-lg font-semibold text-white">Cumpleaños de cine</h3>
                            <p class="text-slate-400 text-sm leading-relaxed">
                                Si hoy es tu cumpleaños y tienes la fecha informada en tu perfil, Filmness te activa automáticamente un descuento especial para celebrarlo en el cine.
                            </p>
                            <span class="mt-auto inline-block rounded-full bg-yellow-500/10 border border-yellow-500/30 px-4 py-1 text-xs font-semibold text-yellow-300 w-fit">Descuento del 10% por cumpleaños</span>
                        </div>

                        <!-- Volume discount -->
                        <div class="flex flex-col gap-4 rounded-2xl border border-white/10 bg-slate-800/60 p-7 hover:border-yellow-500/40 transition">
                            <div class="discount-icon-badge w-12 h-12 rounded-full flex items-center justify-center text-2xl"><img src="/images/tarjeta.png" class="w-7 h-7 object-contain"/></div>
                            <h3 class="text-lg font-semibold text-white">Premiamos las compras grandes</h3>
                            <p class="text-slate-400 text-sm leading-relaxed">
                                Si completas una compra superior a <span class="text-yellow-300 font-semibold">50 €</span>, recibirás un nuevo descuento del 10% para usarlo en una futura compra.
                            </p>
                            <span class="mt-auto inline-block rounded-full bg-yellow-500/10 border border-yellow-500/30 px-4 py-1 text-xs font-semibold text-yellow-300 w-fit">Descuento del 10% por +50€</span>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </Layout>
</template>