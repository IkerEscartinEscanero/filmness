<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

// Props para poder cargar las películas
const props = defineProps({
  movie: Object
});

// Atributos para saber modificar aspectos del diseño al pasar el ratón por encima de una película
const isHovered = ref(false);
const showPreview = ref(false);

let timeout = null

const handleEnter = () => {
  isHovered.value = true
  timeout = setTimeout(() => {
    showPreview.value = true
  }, 300)
}

const handleLeave = () => {
  isHovered.value = false
  showPreview.value = false
  clearTimeout(timeout)
}

const goToMovie = () => {
  router.visit(`/movies/${props.movie.id}`)
};
</script>

<template>
  <div
    class="relative w-full max-w-[240px] aspect-[2/3] overflow-hidden rounded-3xl border border-white/10 bg-zinc-950 shadow-2xl shadow-black/30 transition-transform duration-300 hover:-translate-y-1 hover:scale-[1.02] hover:shadow-primary/30 group cursor-pointer"
    @mouseenter="handleEnter"
    @mouseleave="handleLeave"
    @click="goToMovie"
  >
    <img
      :src="'/' + movie.poster"
      class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105"
    />

    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>

    <div class="absolute inset-0 bg-black/20 opacity-0 transition duration-300 group-hover:opacity-100"></div>

    <div class="absolute bottom-0 left-0 right-0 p-4">
      <div class="rounded-3xl border border-white/10 bg-black/60 px-4 py-3 backdrop-blur-xl">
        <h2 class="text-white text-lg font-semibold leading-tight">
          {{ movie.title }}
        </h2>
      </div>
    </div>
  </div>

  <div
    v-if="showPreview && movie.trailer"
    class="fixed inset-0 flex items-center justify-center z-50 pointer-events-none"
  >
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    <div class="relative w-[60%] max-w-4xl rounded-3xl overflow-hidden shadow-2xl">
      <video
        :src="'/' + movie.trailer"
        autoplay
        muted
        loop
        class="w-full h-full object-cover"
      />
    </div>
  </div>
</template>