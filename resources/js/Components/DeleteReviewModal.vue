<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['cancel', 'confirm']);
</script>

<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="$emit('cancel')"></div>
            <div class="relative mx-4 w-full max-w-sm rounded-2xl border border-slate-700 bg-slate-800 p-8 shadow-2xl">
                <div class="flex flex-col items-center gap-4 text-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-500/10 text-3xl">✘</div>
                    <h2 class="text-xl font-semibold text-white">¿Borrar reseña?</h2>
                    <p class="text-sm text-slate-400">Esta acción no se puede deshacer. La reseña se eliminará permanentemente.</p>
                    <div class="mt-2 flex w-full gap-3">
                        <button
                            type="button"
                            @click="$emit('cancel')"
                            class="flex-1 rounded-xl bg-slate-700 px-4 py-2 text-white transition-colors hover:bg-slate-600"
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            :disabled="processing"
                            @click="$emit('confirm')"
                            class="flex-1 rounded-xl bg-red-500 px-4 py-2 font-semibold text-white transition-colors hover:bg-red-400 disabled:opacity-50"
                        >
                            {{ processing ? 'Borrando...' : 'Borrar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>