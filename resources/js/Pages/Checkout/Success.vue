<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    purchase: Object,
    payment: Object,
    film: Object,
    session: Object,
    seats: { type: Array, default: () => [] },
    googleCalendarUrl: String,
});

const isPaid = computed(() => props.purchase?.status === 'pagado');

// Auto-refresh every 2 seconds if payment is confirmed but seats haven't appeared yet
// (webhook is processing in the background)
const pollInterval = ref(null);

onMounted(() => {
    // If purchase is paid but no seats yet, poll for updates (webhook still processing)
    if (isPaid.value && props.seats.length === 0) {
        pollInterval.value = setInterval(() => {
            router.reload({ only: ['purchase', 'seats'] });
        }, 2000);
    }
});

onUnmounted(() => {
    if (pollInterval.value) clearInterval(pollInterval.value);
});

function formatDateTime(iso) {
    if (!iso) return null;

    const date = new Date(iso);

    return `${date.toLocaleDateString('es-ES', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })} · ${date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}`;
}
</script>

<template>
    <Layout>
        <Head title="Pago recibido" />

        <main class="min-h-screen bg-[#0F172A] text-white py-16 px-4">
            <div class="max-w-3xl mx-auto rounded-3xl border border-white/5 bg-slate-800/70 p-8 md:p-10 text-center">
                <p class="text-xs uppercase tracking-[0.35em] text-emerald-400">Compra realizada</p>
                <h1 class="mt-3 text-3xl font-bold">
                    {{ isPaid ? 'Pago confirmado' : 'Estamos confirmando tu pago' }}
                </h1>
                <p class="mt-4 text-slate-300 leading-relaxed">
                    {{
                        isPaid
                            ? 'Todo correcto. Tus entradas con QR se han generado y te las acabamos de enviar al correo indicado.'
                            : 'Hemos recibido tu compra correctamente. En unos instantes validaremos el pago y te enviaremos las entradas con QR por email.'
                    }}
                </p>

                <div class="mt-8 rounded-2xl bg-slate-900/60 border border-white/5 p-6 text-left">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Resumen de tu compra</p>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-slate-400">Pedido</p>
                            <p class="mt-1 font-semibold text-white">#{{ purchase.id }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Total pagado</p>
                            <p class="mt-1 font-semibold text-white">{{ purchase.total.toFixed(2) }} €</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Correo de envío</p>
                            <p class="mt-1 font-semibold text-white break-all">{{ purchase.contactEmail }}</p>
                        </div>
                        <div v-if="film?.title">
                            <p class="text-slate-400">Película</p>
                            <p class="mt-1 font-semibold text-white">{{ film.title }}</p>
                        </div>
                        <div v-if="session?.date" class="md:col-span-2">
                            <p class="text-slate-400">Sesión</p>
                            <p class="mt-1 font-semibold text-white">{{ formatDateTime(session.date) }}</p>
                        </div>
                        <div v-if="seats.length > 0" class="md:col-span-2">
                            <p class="text-slate-400">Butacas</p>
                            <p class="mt-1 font-semibold text-white">{{ seats.join(', ') }}</p>
                        </div>
                    </div>
                </div>

                <div v-if="googleCalendarUrl" class="mt-8 flex flex-col items-center gap-2">
                    <p class="text-sm text-slate-400">¿No quieres olvidarte de la película?</p>
                    <a
                        :href="googleCalendarUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 rounded-2xl bg-[#4285f4] px-6 py-3 font-semibold text-white hover:bg-[#3367d6] transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 4h-1V2h-2v2H8V2H6v2H5C3.9 4 3 4.9 3 6v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11zM5 7V6h14v1H5z"/>
                        </svg>
                        Añadir a Google Calendar
                    </a>
                </div>

                <div class="mt-8 flex justify-center gap-3 flex-wrap">
                    <Link :href="route('home')" class="rounded-2xl bg-yellow-500 px-5 py-3 font-semibold text-slate-950 hover:bg-yellow-400 transition">
                        Volver al inicio
                    </Link>
                </div>
            </div>
        </main>
    </Layout>
</template>