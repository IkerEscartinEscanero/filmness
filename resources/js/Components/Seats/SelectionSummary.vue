<script setup>
const props = defineProps({
    selectedCount: { type: Number, default: 0 },
    selectedSeatsText: { type: String, default: '' },
    totalPrice: { type: [String, Number], default: '0.00' },
    seatPrice: { type: Number, default: 0 },
});

const emit = defineEmits(['clear', 'checkout']);
</script>

<template>
    <div class="bg-slate-800/70 rounded-2xl border border-white/5 p-6 flex flex-col gap-4">
        <div v-if="selectedCount === 0" class="text-slate-500 text-sm text-center">
            Selecciona las butacas que deseas reservar.
        </div>

        <template v-else>
            <div class="flex flex-wrap gap-4 justify-between items-center">
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-widest mb-1">Butacas seleccionadas</p>
                    <p class="text-white font-semibold text-sm">{{ selectedSeatsText }}</p>
                </div>
                <div class="text-right">
                    <p class="text-slate-400 text-xs uppercase tracking-widest mb-1">Total</p>
                    <p class="text-yellow-400 font-bold text-xl">{{ totalPrice }}€</p>
                    <p class="text-slate-500 text-xs">{{ selectedCount }} × {{ seatPrice.toFixed(2) }}€</p>
                </div>
            </div>

            <div class="flex gap-3">
                <button
                    @click="emit('clear')"
                    class="cursor-pointer flex-1 py-2.5 rounded-xl border border-slate-600 text-slate-400 text-sm hover:border-slate-400 hover:text-white transition"
                >
                    Limpiar selección
                </button>
                <button
                    @click="emit('checkout')"
                    class="cursor-pointer flex-[2] py-2.5 rounded-xl bg-yellow-500 text-slate-950 font-bold text-sm hover:bg-yellow-400 transition shadow-lg shadow-yellow-500/20"
                >
                    Continuar con la compra
                </button>
            </div>
        </template>
    </div>
</template>
