<script setup>
const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const formatDate = (value) => {
    if (!value) {
        return 'No indicado';
    }

    if (typeof value !== 'string') {
        return String(value);
    }

    const [year, month, day] = value.split('-');

    if (!year || !month || !day) {
        return value;
    }

    return `${day}/${month}/${year}`;
};
</script>

<template>
    <section class="overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70 p-6 md:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:gap-8">
            <div class="flex shrink-0 justify-center lg:justify-start">
                <div class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-full border border-white/10 bg-slate-800 md:h-32 md:w-32">
                    <img
                        v-if="user.avatar_url"
                        :src="user.avatar_url"
                        :alt="user.name"
                        class="h-full w-full object-cover"
                    />
                    <span v-else class="text-4xl text-slate-300">👤</span>
                </div>
            </div>

            <div class="min-w-0 flex-1 space-y-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-yellow-300/80">Perfil</p>
                    <h2 class="mt-2 text-2xl font-semibold text-white md:text-3xl">{{ user.name }}</h2>
                </div>

                <div class="grid gap-3 md:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Email</p>
                        <p class="mt-2 break-all text-sm font-medium text-white">{{ user.email }}</p>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Cumpleaños</p>
                        <p class="mt-2 text-sm font-medium text-white">{{ formatDate(user.birth_date) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>