<script setup>

</script>

<template>
<div>
    <h2 class="text-xl font-bold mb-4">🎬 Películas vistas</h2>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <div v-for="movie in movies" :key="movie.id"
             class="card bg-base-100 shadow hover:scale-105 transition">

            <figure>
                <img :src="movie.poster" class="h-40 object-cover"/>
            </figure>

            <div class="p-2 text-center text-sm">
                {{ movie.title }}
            </div>
        </div>
    </div>
</div>
</template>