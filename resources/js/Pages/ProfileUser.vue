<script setup>
    import ProfileInfo from '@/Components/Profile/ProfileInfo.vue'
    import MoviesWatched from '@/Components/Profile/MoviesWatched.vue'
    import ReviewsList from '@/Components/Profile/ReviewsList.vue'
    import DiscountsList from '@/Components/Profile/DiscountsList.vue'

    defineProps({
        user: Object,
        movies: Array,
        reviews: Array,
        discounts: Array
    })
</script>

<template>
    <div class="min-h-screen bg-base-200 p-6">

        <div class="max-w-7xl mx-auto space-y-10">

            <!-- Perfil -->
            <ProfileInfo :user="user" />

            <!-- Películas -->
            <MoviesWatched :movies="movies" />

            <!-- Reseñas -->
            <ReviewsList :reviews="reviews" />

            <!-- Descuentos -->
            <DiscountsList :discounts="discounts" />

        </div>

    </div>
</template>