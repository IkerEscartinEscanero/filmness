<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import mascaras from '@/assets/svg/mascaras.svg';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    birth_date: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Registrarse" />

    <main class="relative min-h-screen bg-slate-950 text-white overflow-hidden">
        <!-- Elementos decorativos llamativos -->
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
                        <p class="text-sm uppercase tracking-[0.4em] text-yellow-300">Crea tu cuenta</p>
                        <h2 class="mt-3 text-3xl font-semibold text-white">Regístrate en FilmNess</h2>
                        <p class="mt-3 text-slate-400">
                            Completa el formulario para acceder a contenido exclusivo y recibir descuentos exclusivos.
                        </p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="name" value="Nombre *" class="text-white font-medium" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full text-white bg-slate-800 border-slate-600 focus:border-yellow-500"
                            v-model="form.name"
                            placeholder="Tu nombre"
                            required
                            autocomplete="name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="birth_date" value="Fecha de nacimiento" class="text-white font-medium" />
                        <TextInput
                            id="birth_date"
                            type="date"
                            class="mt-1 block w-full text-white bg-slate-800 border-slate-600 focus:border-yellow-500"
                            v-model="form.birth_date"
                            autocomplete="bday"
                        />
                        <InputError class="mt-2" :message="form.errors.birth_date" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email *" class="text-white font-medium" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full text-white bg-slate-800 border-slate-600 focus:border-yellow-500"
                            v-model="form.email"
                            placeholder="tuemail@ejemplo.com"
                            required
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
                            placeholder="Contraseña segura"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Confirmar contraseña *" class="text-white font-medium" />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-1 block w-full text-white bg-slate-800 border-slate-600 focus:border-yellow-500"
                            v-model="form.password_confirmation"
                            placeholder="Repite la contraseña"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <Link
                            :href="route('login')"
                            class="text-sm text-yellow-300 underline transition hover:text-yellow-100"
                        >
                            ¿Ya tienes cuenta?
                        </Link>

                        <PrimaryButton
                            class="w-full rounded-full bg-yellow-500 px-8 py-3 text-slate-950 transition hover:bg-yellow-400 justify-center w-full max-w-220 sm:w-auto"
                            :class="{ 'opacity-70': form.processing }"
                            :disabled="form.processing"
                        >
                            Crear cuenta
                        </PrimaryButton>
                    </div>
                </form>
            </section>
        </div>
    </main>
</template>
