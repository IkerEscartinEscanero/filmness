<script setup>

</script>

<template>
<div>
    <h2 class="text-xl font-bold mb-4">⭐ Reseñas</h2>

    <div class="space-y-4">
        <div v-for="review in reviews" :key="review.id"
             class="card bg-base-100 shadow p-4">

            <div class="flex justify-between items-center mb-2">
                <span class="font-semibold">{{ review.film.title }}</span>
                <span class="text-yellow-400">
                    ⭐ {{ review.stars }}/5
                </span>
            </div>

            <p class="text-sm opacity-80">
                {{ review.comment }}
            </p>
        </div>
    </div>
</div>
</template>