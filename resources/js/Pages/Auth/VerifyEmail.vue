<script setup>
import { computed } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import mascaras from '@/assets/svg/mascaras.svg';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <Head title="Verificar email" />

    <main class="user-management-page relative min-h-screen bg-slate-950 text-white overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900/60 via-transparent to-slate-800/60 pointer-events-none"></div>
        <div class="absolute right-0 top-24 h-64 w-64 rounded-full bg-slate-700/30 blur-3xl pointer-events-none animate-pulse"></div>
        <div class="absolute left-0 bottom-0 h-64 w-64 rounded-full bg-slate-800/70 blur-3xl pointer-events-none animate-pulse"></div>
        <div class="absolute left-1/4 top-1/4 h-44 w-44 rounded-full bg-slate-600/20 blur-2xl pointer-events-none animate-pulse"></div>
        <div class="absolute right-1/4 bottom-1/4 h-52 w-52 rounded-full bg-slate-700/50 blur-3xl pointer-events-none animate-pulse"></div>

        <div class="relative mx-auto flex min-h-screen max-w-4xl items-center px-6 py-10">
            <section class="w-full rounded-[2rem] border border-white/10 bg-slate-950/95 p-10 shadow-[0_30px_90px_-40px_rgba(0,0,0,0.8)] backdrop-blur-xl">
                <div class="mb-8 flex items-center gap-4">
                    <img :src="mascaras" class="w-16 h-16" alt="Máscaras de teatro" />
                    <div>
                        <p class="text-sm uppercase tracking-[0.4em] text-yellow-300">Verifica tu cuenta</p>
                        <h2 class="mt-3 text-3xl font-semibold text-white">Confirma tu email</h2>
                        <p class="mt-3 text-slate-400">
                            Gracias por registrarte. Verifica tu email para acceder a FilmNess.
                        </p>
                    </div>
                </div>

                <div class="mb-4 text-sm text-slate-300">
                    Antes de comenzar, verifica tu dirección de email haciendo clic en el enlace que te hemos enviado. Si no lo recibiste, te enviaremos otro.
                </div>

                <div
                    class="mb-4 text-sm font-medium text-emerald-200"
                    v-if="verificationLinkSent"
                >
                    Un nuevo enlace de verificación ha sido enviado a tu email.
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <PrimaryButton
                            class="w-full max-w-[220px] rounded-full bg-yellow-500 px-8 py-3 text-slate-950 transition hover:bg-yellow-400 justify-center"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Reenviar email de verificación
                        </PrimaryButton>

                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="rounded-full border border-slate-600 bg-slate-800 px-6 py-3 text-sm text-slate-300 transition hover:bg-yellow-500 hover:text-slate-950 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        >
                            Cerrar sesión
                        </Link>
                    </div>
                </form>
            </section>
        </div>
    </main>
</template>
