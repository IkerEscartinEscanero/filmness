<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    session: Object,
    film: Object,
    selectedSeats: Array,
    total: Number,
    contactEmail: String,
    stripeKeyConfigured: Boolean,
    cancelled: Boolean,
});

const form = useForm({
    email: props.contactEmail ?? '',
    seat_ids: props.selectedSeats.map((seat) => seat.id),
});

const selectedSeatLabels = computed(() => props.selectedSeats.map((seat) => seat.label).join(', '));
const selectedCount = computed(() => props.selectedSeats.length);
const unitPrice = computed(() => Number(props.session.price).toFixed(2));
const totalPrice = computed(() => Number(props.total).toFixed(2));

function formatDateTime(iso) {
    const date = new Date(iso);

    return `${date.toLocaleDateString('es-ES', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })} · ${date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}`;
}

function submit() {
    form.post(route('checkout.store', { session: props.session.id }));
}
</script>

<template>
    <Layout>
        <Head title="Resumen de compra" />

        <main class="min-h-screen bg-[#0F172A] text-white py-10 px-4">
            <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-[1.3fr_0.8fr] gap-8">
                <section class="bg-slate-800/70 border border-white/5 rounded-3xl p-6 md:p-8">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <div>
                            <p class="text-xs uppercase tracking-[0.3em] text-yellow-400">Paso 2</p>
                            <h1 class="mt-2 text-3xl font-bold">Resumen y pago</h1>
                            <p class="mt-2 text-slate-300 max-w-2xl text-sm">
                                Estás a punto de completar el pago. Nos gustaría que te asegurases de que 
                                <strong>esas son las butacas que vas a comprar</strong> y nos escribieses <strong>el correo</strong> 
                                al que enviaremos las entradas y el código QR. En caso de querer cambiar las butacas, puedes pulsar el 
                                botón de <i>"Cambiar butacas"</i> para volver a la selección sin perder el correo ni las butacas seleccionadas. 
                                Si todo es correcto, pulsa el botón de pago para continuar con Stripe.
                            </p>
                        </div>

                        <Link
                            :href="route('sessions.seats', { session: session.id })"
                            :data="{ seat_ids: form.seat_ids }"
                            as="button"
                            class="rounded-xl border border-slate-600 px-4 py-2 text-sm text-slate-300 hover:border-slate-400 hover:text-white transition"
                        >
                            Cambiar butacas
                        </Link>
                    </div>

                    <div
                        v-if="cancelled"
                        class="mt-6 rounded-2xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-200"
                    >
                        El pago fue cancelado. Puedes revisar el resumen y volver a intentarlo cuando quieras.
                    </div>

                    <div class="mt-8 grid gap-5">
                        <div class="rounded-2xl bg-slate-900/60 border border-white/5 p-5">
                            <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Sesión</p>
                            <h2 class="mt-2 text-xl font-semibold">{{ film.title }}</h2>
                            <p class="mt-2 text-sm text-slate-300">{{ formatDateTime(session.date) }}</p>
                            <p class="mt-1 text-sm text-slate-400">{{ session.room }}</p>
                        </div>

                        <div class="rounded-2xl bg-slate-900/60 border border-white/5 p-5">
                            <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Entradas seleccionadas</p>
                            <p class="mt-2 text-lg font-semibold text-white">{{ selectedSeatLabels }}</p>
                            <p class="mt-2 text-sm text-slate-400">{{ selectedCount }} entrada<span v-if="selectedCount !== 1">s</span> a {{ unitPrice }} €</p>
                        </div>

                        <form class="rounded-2xl bg-slate-900/60 border border-white/5 p-5" @submit.prevent="submit">
                            <label class="block">
                                <span class="text-xs uppercase tracking-[0.25em] text-slate-400">Correo para recibir las entradas</span>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    autocomplete="email"
                                    required
                                    class="mt-3 w-full rounded-2xl border border-slate-600 bg-slate-950 px-4 py-3 text-white placeholder:text-slate-500 focus:border-yellow-500 focus:outline-none"
                                    placeholder="tu@email.com"
                                >
                            </label>

                            <p class="mt-3 text-xs text-slate-500">
                                Tras el pago, las entradas y su QR se enviarán a este correo. Asegúrate de que es correcto y de tener acceso a él para poder disfrutar de tu película.
                            </p>

                            <p v-if="form.errors.email" class="mt-3 text-sm text-red-400">{{ form.errors.email }}</p>
                            <p v-if="form.errors.payment" class="mt-3 text-sm text-red-400">{{ form.errors.payment }}</p>

                            <div
                                v-if="!stripeKeyConfigured"
                                class="mt-4 rounded-2xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-200"
                            >
                                Falta configurar STRIPE_KEY y STRIPE_SECRET en el entorno. La pantalla ya está conectada, pero Stripe no podrá iniciarse hasta estén esas claves.
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing || !stripeKeyConfigured"
                                class="mt-6 w-full rounded-2xl bg-yellow-500 px-5 py-3 text-base font-bold text-slate-950 transition"
                                :class="form.processing || !stripeKeyConfigured
                                    ? 'cursor-not-allowed opacity-60'
                                    : 'hover:bg-yellow-400 shadow-lg shadow-yellow-500/20 cursor-pointer'"
                            >
                                {{ form.processing ? 'Redirigiendo a Stripe...' : 'Pagar con Stripe' }}
                            </button>
                        </form>
                    </div>
                </section>

                <aside class="bg-slate-800/70 border border-white/5 rounded-3xl p-6 h-fit lg:sticky lg:top-8">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Resumen</p>
                    <div class="mt-5 space-y-4 text-sm">
                        <div class="flex justify-between gap-4 text-slate-300">
                            <span>{{ selectedCount }} entrada<span v-if="selectedCount !== 1">s</span></span>
                            <span>{{ unitPrice }} €</span>
                        </div>
                        <div class="h-px bg-white/10"></div>
                        <div class="flex justify-between gap-4 text-white text-lg font-semibold">
                            <span>Total</span>
                            <span>{{ totalPrice }} €</span>
                        </div>
                    </div>

                    <div class="mt-8 rounded-2xl bg-slate-900/60 border border-white/5 p-4 text-sm text-slate-400">
                        Stripe gestionará el formulario de tarjeta y la autenticación segura. Tu aplicación solo iniciará la compra y recibirá la confirmación.
                    </div>
                </aside>
            </div>
        </main>
    </Layout>
</template>