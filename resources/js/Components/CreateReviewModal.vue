<script setup>
import Modal from '@/Components/Modal.vue';

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    stars: {
        type: Number,
        default: 0,
    },
    comment: {
        type: String,
        default: '',
    },
    processing: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

defineEmits(['close', 'submit', 'update:stars', 'update:comment']);
</script>

<template>
    <Modal :show="show" max-width="md" @close="$emit('close')">
        <div class="bg-slate-900 p-6 text-white">
            <h3 class="mb-2 text-xl font-semibold text-yellow-300">Dejar reseña</h3>
            <p class="mb-6 text-sm text-slate-400">Comparte tu opinión sobre esta película.</p>

            <form class="space-y-4" @submit.prevent="$emit('submit')">
                <div>
                    <label class="mb-3 block text-sm font-semibold text-slate-200">Valoración</label>
                    <div class="flex gap-3">
                        <button
                            v-for="star in 5"
                            :key="star"
                            type="button"
                            @click="$emit('update:stars', star)"
                            class="text-3xl transition"
                            :class="star <= stars ? 'text-yellow-400' : 'text-slate-600'"
                        >
                            ★
                        </button>
                    </div>
                </div>

                <div>
                    <label for="comment" class="mb-2 block text-sm font-semibold text-slate-200">Comentario (opcional)</label>
                    <textarea
                        id="comment"
                        :value="comment"
                        @input="$emit('update:comment', $event.target.value)"
                        placeholder="Cuéntanos qué te pareció..."
                        class="h-24 w-full resize-none rounded-xl border border-slate-600 bg-slate-800 px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-yellow-500"
                    ></textarea>
                    <p v-if="errors.comment" class="mt-1 text-sm text-red-400">{{ errors.comment }}</p>
                </div>

                <div class="flex gap-3 pt-4">
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="flex-1 rounded-xl border border-slate-600 px-4 py-2 text-slate-300 transition hover:bg-slate-800"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        :disabled="processing"
                        class="flex-1 rounded-xl bg-yellow-500 px-4 py-2 font-semibold text-slate-950 transition hover:bg-yellow-400 disabled:opacity-50"
                    >
                        {{ processing ? 'Guardando...' : 'Guardar reseña' }}
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>