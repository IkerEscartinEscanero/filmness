<script setup>

</script>

<template>
<div>
    <h2 class="text-xl font-bold mb-4">🎟️ Descuentos</h2>

    <div class="flex flex-wrap gap-4">
        <div v-for="discount in discounts" :key="discount.id"
             class="badge badge-primary p-4 text-lg">

            {{ discount.value }}%
        </div>
    </div>
</div>
</template>