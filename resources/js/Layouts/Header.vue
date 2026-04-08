<script setup>
    import Nav from '@/Layouts/Nav.vue';
    import { Link } from '@inertiajs/vue3';
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
    <div class="sticky top-0 z-50 bg-slate-900 backdrop-blur-md border-b border-yellow-500/20 shadow-lg transition-all duration-300">
        <div class="px-6 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-3">
                <Link :href="route('home')">
                    <img src="/images/Logo.png" class="w-16 h-16 md:w-24 md:h-24 transition-transform duration-300 hover:scale-110" />
                </Link>
                <Link :href="route('home')" class="text-yellow-500 font-bold text-2xl md:text-3xl tracking-wide">FilmNess</Link>
            </div>

            <!-- Principal header in desktop -->
            <div class="hidden md:flex gap-6 items-center text-sm">
                <Link :href="route('home')" class="text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium cursor-pointer">Inicio</Link>
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
                    <Transition
                        enter-active-class="transition-all duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95 -translate-y-1"
                        enter-to-class="opacity-100 scale-100 translate-y-0"
                        leave-active-class="transition-all duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100 translate-y-0"
                        leave-to-class="opacity-0 scale-95 -translate-y-1"
                    >
                        <div
                            v-if="showNav"
                            class="absolute right-0 mt-2 bg-slate-800 border border-yellow-500/20 rounded-lg shadow-xl backdrop-blur-md z-50"
                            @click.stop
                        >
                            <Nav @close="closeNav" />
                        </div>
                    </Transition>
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
                    <Transition
                        enter-active-class="transition-all duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95 -translate-y-1"
                        enter-to-class="opacity-100 scale-100 translate-y-0"
                        leave-active-class="transition-all duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100 translate-y-0"
                        leave-to-class="opacity-0 scale-95 -translate-y-1"
                    >
                        <div
                            v-if="showNav"
                            class="absolute right-0 mt-2 bg-slate-800 border border-yellow-500/20 rounded-lg shadow-xl backdrop-blur-md z-50"
                            @click.stop
                        >
                            <Nav @close="closeNav" />
                        </div>
                    </Transition>
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
            <Link :href="route('home')" class="text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium">Inicio</Link>
            <a class="text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium">Cartelera</a>
            <a class="text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium">Estrenos</a>
            <a class="text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium">Sobre nosotros</a>
        </div>
    </div>
</template>