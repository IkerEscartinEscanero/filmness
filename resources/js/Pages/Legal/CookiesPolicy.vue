<script setup>
    import Layout from '@/Layouts/Layout.vue';
    import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Layout>
        <Head title="Política de cookies" />

        <main class="min-h-screen bg-[#0F172A] text-white">
            <section class="mx-auto max-w-5xl px-6 py-12 md:py-16">
                <p class="text-sm uppercase tracking-[0.35em] text-yellow-300">Legal</p>
                <h1 class="mt-3 text-3xl font-bold md:text-4xl">Política de cookies</h1>
                <p class="mt-3 text-sm text-slate-400">https://filmness.onrender.com/</p>

                <div class="mt-8 space-y-6">
                    <article class="rounded-2xl border border-white/10 bg-slate-900/70 p-6">
                        <h2 class="text-xl font-semibold text-white">Información general</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            El acceso a este sitio web puede implicar la utilización de cookies. Las cookies son
                            pequeños archivos que se almacenan en el navegador del usuario para que el servidor recuerde
                            cierta información y facilite una navegación más fluida.
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            En FilmNess utilizamos únicamente cookies propias y técnicas necesarias para el correcto
                            funcionamiento del sitio. No utilizamos cookies de analítica de terceros como Google
                            Analytics, ni cookies de publicidad o perfiles de comportamiento.
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            La información recogida puede incluir datos como la fecha y hora de acceso, páginas visitadas
                            o tiempo de permanencia. Estas cookies no permiten identificar directamente al usuario por
                            sí solas ni extraer información del dispositivo.
                        </p>
                    </article>

                    <article class="rounded-2xl border border-white/10 bg-slate-900/70 p-6">
                        <h2 class="text-xl font-semibold text-white">Cookies propias</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            Son las cookies enviadas al dispositivo del usuario y gestionadas exclusivamente por FilmNess
                            para el mejor funcionamiento del sitio web.
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            En este proyecto, estas cookies están vinculadas al funcionamiento normal de Laravel y del
                            flujo de navegación, como la sesión y la protección CSRF, además de posibles preferencias
                            técnicas necesarias para el uso correcto del sitio.
                        </p>
                        <ul class="mt-3 list-disc space-y-2 pl-5 text-sm leading-7 text-slate-300">
                            <li>Cookies de sesión para mantener la navegación del usuario.</li>
                            <li>Cookies técnicas de seguridad y protección frente a solicitudes no válidas.</li>
                            <li>Cookies necesarias para que el sitio funcione correctamente.</li>
                        </ul>
                    </article>

                    <article class="rounded-2xl border border-white/10 bg-slate-900/70 p-6">
                        <h2 class="text-xl font-semibold text-white">Cookies de terceros</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            FilmNess no utiliza cookies de terceros de analítica ni de publicidad durante la navegación
                            habitual del sitio web.
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            En caso de que el usuario acceda a servicios externos de pago o enlaces ajenos al sitio,
                            dichos terceros podrán gestionar sus propias cookies fuera del dominio de FilmNess, bajo sus
                            propias políticas de privacidad y cookies.
                        </p>
                    </article>

                    <article class="rounded-2xl border border-white/10 bg-slate-900/70 p-6">
                        <h2 class="text-xl font-semibold text-white">Deshabilitar, rechazar y eliminar cookies</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            El usuario puede deshabilitar, rechazar y eliminar las cookies instaladas en su dispositivo
                            mediante la configuración de su navegador. Si rechaza las cookies, podrá seguir usando el
                            sitio, aunque algunas funcionalidades podrían quedar limitadas.
                        </p>
                    </article>

                    <article class="rounded-2xl border border-white/10 bg-slate-900/70 p-6">
                        <h2 class="text-xl font-semibold text-white">Actualización</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            Este documento de Política de Cookies ha sido adaptado al funcionamiento real de FilmNess y
                            revisado el 22/05/2026.
                        </p>
                    </article>
                </div>
            </section>
        </main>
    </Layout>
</template>
