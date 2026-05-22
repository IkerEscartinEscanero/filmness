<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import mascaras from '@/assets/svg/mascaras.svg';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Restablecer contraseña" />

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
                        <p class="text-sm uppercase tracking-[0.4em] text-yellow-300">Restablece tu acceso</p>
                        <h2 class="mt-3 text-3xl font-semibold text-white">Restablecer contraseña</h2>
                        <p class="mt-3 text-slate-400">
                            Introduce tu correo y la nueva contraseña para recuperar el acceso a FilmNess.
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
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="mt-4 flex justify-end">
                        <PrimaryButton
                            class="w-full max-w-[220px] rounded-full bg-yellow-500 px-8 py-3 text-slate-950 transition hover:bg-yellow-400 justify-center"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Reset Password
                        </PrimaryButton>
                    </div>
                </form>
            </section>
        </div>
    </main>
</template>
