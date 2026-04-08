<script setup>
    import Nav from '@/Layouts/Nav.vue';
    import { ref } from 'vue';

    // State to show or hide the profile submenu
    const showNav = ref(false);
    const showMobileMenu = ref(false);

    // Function to toggle the submenu
    const toggleNav = () => {
        showNav.value = !showNav.value;
    };

    // Function to close the submenu
    const closeNav = () => {
        showNav.value = false;
    };
</script>

<template>
    <div class="bg-slate-900 backdrop-blur-md border-b border-yellow-500/20 shadow-lg transition-all duration-300">
        <div class="px-6 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-3">
                <img src="/images/Logo.png" class="w-16 h-16 md:w-24 md:h-24 transition-transform duration-300 hover:scale-110" />
                <span class="text-yellow-500 font-bold text-2xl md:text-3xl tracking-wide">FilmNess</span>
            </div>

            <!-- Principal header in desktop -->
            <div class="hidden md:flex gap-6 items-center text-sm">
                <a class="text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium cursor-pointer">Inicio</a>
                <a class="text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium cursor-pointer">Cartelera</a>
                <a class="text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium cursor-pointer">Próximos estrenos</a>
                <a class="text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium cursor-pointer">Sobre nosotros</a>

                <!-- Dark mode -->
                <button class="btn btn-sm btn-ghost text-slate-300 hover:text-yellow-500 transition-colors duration-300">
                    🌙
                </button>

                <!-- Perfil -->
                <div class="relative">
                    <button @click="toggleNav" class="btn btn-circle btn-outline border-yellow-500 text-yellow-500 hover:bg-yellow-500 hover:text-slate-900 transition-all duration-300">
                        <img src="/images/FotoPerfil.png" class="w-9 h-9 rounded-full" />
                    </button>
                    <div
                        v-if="showNav"
                        class="absolute right-0 mt-2 bg-slate-800 border border-yellow-500/20 rounded-lg shadow-xl backdrop-blur-md z-50 transition-all duration-300 transform opacity-0 scale-95"
                        :class="{ 'opacity-100 scale-100': showNav }"
                        @click.stop
                    >
                        <Nav :canLogin="canLogin" :canRegister="canRegister" @close="closeNav" />
                    </div>
                </div>

                <!-- Cart -->
                <button class="btn btn-circle bg-yellow-500 text-slate-900 hover:bg-yellow-400 transition-all duration-300 shadow-md">🛒</button>
            </div>

            <!-- MOVIL -->
            <div class="flex md:hidden items-center gap-3">
                <button class="btn btn-sm btn-ghost text-slate-300 hover:text-yellow-500 transition-colors duration-300">🌙</button>
                <button class="btn btn-circle bg-yellow-500 text-slate-900 hover:bg-yellow-400 transition-all duration-300 shadow-md text-sm">🛒</button>

                <!-- Login and register -->
                <div class="relative">
                    <button @click="toggleNav" class="btn btn-circle btn-outline border-yellow-500 text-yellow-500 hover:bg-yellow-500 hover:text-slate-900 transition-all duration-300">
                        <img src="/images/FotoPerfil.png" class="w-8 h-8 rounded-full" />
                    </button>
                    <div
                        v-if="showNav"
                        class="absolute right-0 mt-2 bg-slate-800 border border-yellow-500/20 rounded-lg shadow-xl backdrop-blur-md z-50"
                        @click.stop
                    >
                        <Nav :canLogin="canLogin" :canRegister="canRegister" @close="closeNav" />
                    </div>
                </div>

                <!-- Hamburger for mobile menu -->
                <button @click="showMobileMenu = !showMobileMenu" class="text-slate-300 hover:text-yellow-500 transition-colors duration-300">
                    <svg v-if="!showMobileMenu" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile dropdown menu -->
        <div v-if="showMobileMenu" class="md:hidden border-t border-yellow-500/20 px-6 py-4 flex flex-col gap-4 text-sm">
            <a class="text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium">Inicio</a>
            <a class="text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium">Cartelera</a>
            <a class="text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium">Estrenos</a>
            <a class="text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium">Sobre nosotros</a>
        </div>
    </div>
</template>