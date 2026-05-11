<script setup>
import { computed } from 'vue';

const props = defineProps({
    src: {
        type: String,
        default: null,
    },
    alt: {
        type: String,
        default: 'Avatar de usuario',
    },
    sizeClass: {
        type: String,
        default: 'h-12 w-12',
    },
    innerInsetClass: {
        type: String,
        default: 'inset-[0.42rem]',
    },
    frameSrc: {
        type: String,
        default: '/images/Render1.png',
    },
    fallbackSrc: {
        type: String,
        default: '/images/Render3.png',
    },
});

const avatarSrc = computed(() => props.src || props.fallbackSrc);
</script>

<template>
    <div :class="['relative shrink-0 overflow-visible', sizeClass]">
        <div :class="['absolute z-10 overflow-hidden rounded-full bg-slate-900 shadow-lg shadow-slate-950/40', innerInsetClass, 'inset-0']">
            <img
                :src="avatarSrc"
                :alt="alt"
                class="h-full w-full rounded-full object-cover"
            />
        </div>
        <img
            :src="frameSrc"
            alt="Marco de avatar"
            class="pointer-events-none absolute left-1/2 top-1/2 z-20 h-full w-full -translate-x-1/2 -translate-y-1/2 object-contain"
        />
    </div>
</template>