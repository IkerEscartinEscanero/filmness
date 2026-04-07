<script setup>
    import Checkbox from '@/Components/Checkbox.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';
    import mascaras from '@/assets/svg/mascaras.svg'

    defineProps({
        canResetPassword: {
            type: Boolean,
        },
        status: {
            type: String,
        },
    });

    const form = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = () => {
        form.post(route('login'), {
            onFinish: () => form.reset('password'),
        });
    };
</script>

<template>
    <Head title="Login" />
    
    <main class="min-h-screen bg-slate-950 text-white">
        <!-- Elementos decorativos llamativos -->
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900/50 via-transparent to-slate-800/50 pointer-events-none"></div>
        <div class="absolute right-0 top-28 h-64 w-64 rounded-full bg-slate-700/30 blur-3xl pointer-events-none animate-pulse"></div>
        <div class="absolute left-0 bottom-0 h-64 w-64 rounded-full bg-slate-800/70 blur-3xl pointer-events-none animate-pulse"></div>
        <div class="absolute left-1/4 top-1/4 h-48 w-48 rounded-full bg-slate-600/20 blur-2xl pointer-events-none animate-pulse"></div>
        <div class="absolute right-1/4 bottom-1/4 h-56 w-56 rounded-full bg-slate-700/50 blur-3xl pointer-events-none animate-pulse"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 h-32 w-32 rounded-full bg-slate-600/10 blur-xl pointer-events-none animate-spin"></div>

        <div class="relative mx-auto flex min-h-screen max-w-4xl items-center px-6 py-6">
            <div class="grid w-full gap-10 lg:grid-cols-1">
                <section class="rounded-[2rem] border border-white/10 bg-slate-950/95 p-10 shadow-[0_30px_90px_-40px_rgba(0,0,0,0.8)] backdrop-blur-xl">
                    <div class="mb-8 flex items-center gap-4">
                        <!-- Símbolo de las máscaras -->
                        <img :src="mascaras" class="w-20 h-20" />
                        <div>
                            <p class="text-sm uppercase tracking-[0.4em] text-yellow-300">Bienvenido/a de nuevo a filmness</p>
                            <h2 class="mt-3 text-3xl font-semibold text-white">Inicia sesión</h2>
                            <p class="mt-3 text-slate-400">
                                Introduce tus datos para continuar y acceder a tu perfil personal.
                            </p>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <InputLabel for="email" value="Email *" class="text-white font-medium" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full text-white bg-slate-800 border-slate-600 focus:border-yellow-500"
                                v-model="form.email"
                                placeholder="tuemail@ejemplo.com"
                                required
                                autofocus
                                autocomplete="username"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div>
                            <InputLabel for="password" value="Contraseña *" class="text-white font-medium" />
                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1 block w-full text-white bg-slate-800 border-slate-600 focus:border-yellow-500"
                                v-model="form.password"
                                placeholder="Tu contraseña"
                                required
                                autocomplete="current-password"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <label class="flex items-center gap-3 text-sm text-slate-300">
                                <Checkbox name="remember" v-model:checked="form.remember" />
                                Recordarme
                            </label>

                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-sm text-yellow-300 underline transition hover:text-yellow-100"
                            >
                                ¿Olvidaste tu contraseña?
                            </Link>
                        </div>

                        <PrimaryButton
                            class="w-full rounded-full bg-yellow-500 px-6 py-3 text-slate-950 transition hover:bg-yellow-400 text-black-500"
                            :class="{ 'opacity-70': form.processing }"
                            :disabled="form.processing"
                        >
                            Iniciar sesión
                        </PrimaryButton>
                    </form>
                </section>
            </div>
        </div>
    </main>
</template>