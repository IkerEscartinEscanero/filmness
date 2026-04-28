<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    purchase: Object,
    payment: Object,
});
</script>

<template>
    <Layout>
        <Head title="Pago recibido" />

        <main class="min-h-screen bg-[#0F172A] text-white py-16 px-4">
            <div class="max-w-3xl mx-auto rounded-3xl border border-white/5 bg-slate-800/70 p-8 md:p-10 text-center">
                <p class="text-xs uppercase tracking-[0.35em] text-emerald-400">Stripe</p>
                <h1 class="mt-3 text-3xl font-bold">Hemos recibido el retorno del pago</h1>
                <p class="mt-4 text-slate-300 leading-relaxed">
                    Tu compra ha vuelto correctamente desde Stripe. El siguiente paso será validar el pago de forma definitiva y, después, generar las entradas con QR para enviarlas al correo indicado.
                </p>

                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                    <div class="rounded-2xl bg-slate-900/60 border border-white/5 p-5">
                        <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Compra</p>
                        <p class="mt-2 text-lg font-semibold">#{{ purchase.id }}</p>
                        <p class="mt-2 text-sm text-slate-400">Email: {{ purchase.contactEmail }}</p>
                        <p class="mt-1 text-sm text-slate-400">Total: {{ purchase.total.toFixed(2) }} €</p>
                    </div>

                    <div class="rounded-2xl bg-slate-900/60 border border-white/5 p-5">
                        <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Estado técnico</p>
                        <p class="mt-2 text-sm text-slate-300">Compra: {{ purchase.status }}</p>
                        <p class="mt-1 text-sm text-slate-300">Pasarela: {{ payment.gatewayStatus ?? 'sin datos' }}</p>
                        <p class="mt-1 text-xs text-slate-500 break-all">Stripe session: {{ payment.stripeSessionId || 'pendiente' }}</p>
                    </div>
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