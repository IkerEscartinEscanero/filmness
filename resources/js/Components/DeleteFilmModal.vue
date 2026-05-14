<script setup>
    defineProps({
        show: {
            type: Boolean,
            default: false,
        },
        title: {
            type: String,
            default: '¿Borrar película?',
        },
        message: {
            type: String,
            default: 'Esta acción no se puede deshacer. La película y todos sus archivos asociados serán eliminados permanentemente.',
        },
        confirmLabel: {
            type: String,
            default: 'Borrar',
        },
        cancelLabel: {
            type: String,
            default: 'Cancelar',
        },
    });

    defineEmits(['cancel', 'confirm']);
</script>

<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="$emit('cancel')"></div>
            <div class="relative bg-slate-800 border border-slate-700 rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4">
                <div class="flex flex-col items-center text-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-red-500/10 flex items-center justify-center text-3xl">✘</div>
                    <h2 class="text-xl font-semibold text-white">{{ title }}</h2>
                    <p class="text-slate-400 text-sm">{{ message }}</p>
                    <div class="flex gap-3 w-full mt-2">
                        <button @click="$emit('cancel')" class="flex-1 px-4 py-2 rounded-xl bg-slate-700 text-white hover:bg-slate-600 transition-colors">{{ cancelLabel }}</button>
                        <button @click="$emit('confirm')" class="flex-1 px-4 py-2 rounded-xl bg-red-500 text-white hover:bg-red-400 transition-colors font-semibold">{{ confirmLabel }}</button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>