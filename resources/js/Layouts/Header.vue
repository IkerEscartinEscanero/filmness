<script setup>
    import Nav from '@/Layouts/Nav.vue';
    import { ref } from 'vue';

    // Estado para mostrar u ocultar el submenú de perfil
    const showNav = ref(false);

    // Función para alternar el submenú
    const toggleNav = () => {
        showNav.value = !showNav.value;
    };

    // Función para cerrar el submenú al hacer clic fuera de él
    const closeNav = () => {
        showNav.value = false;
    };
</script>

<template>
    <div class="navbar bg-slate-900 backdrop-blur-md px-6 py-4 border-b border-yellow-500/20 shadow-lg transition-all duration-300">
        <!-- LOGO -->
        <div class="flex-1 flex items-center gap-3">
            <img src="images/Logo.png" class="w-24 h-24 transition-transform duration-300 hover:scale-110" />
            <span class="text-yellow-500 font-bold text-3xl tracking-wide">FilmNess</span>
        </div>

        <!-- NAV PRINCIPAL -->
        <div class="flex gap-6 items-center text-sm">
            <a class="text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium">Inicio</a>
            <a class="text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium">Cartelera</a>
            <a class="text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium">Estrenos</a>
            <a class="text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium">Sobre nosotros</a>

            <!-- DARK MODE -->
            <button class="btn btn-sm btn-ghost text-slate-300 hover:text-yellow-500 transition-colors duration-300">
                🌙
            </button>

            <!-- PERFIL -->
            <div class="relative">
                <button @click="toggleNav" class="btn btn-circle btn-outline border-yellow-500 text-yellow-500 hover:bg-yellow-500 
                                                    hover:text-slate-900 transition-all duration-300">
                    <img src="images/FotoPerfil.png" class="w-9 h-9 rounded-full" />
                </button>

                <!-- SUBMENÚ NAV -->
                <div
                    v-if="showNav"
                    class="absolute right-0 mt-2 bg-slate-800 border border-yellow-500/20 rounded-lg shadow-xl backdrop-blur-md 
                            z-50 transition-all duration-300 transform opacity-0 scale-95"
                    :class="{ 'opacity-100 scale-100': showNav }"
                    @click.stop
                >
                    <Nav
                        :canLogin="canLogin"
                        :canRegister="canRegister"
                        @close="closeNav"
                    />
                </div>
            </div>

            <!-- CARRITO -->
            <button class="btn btn-circle bg-yellow-500 text-slate-900 hover:bg-yellow-400 transition-all duration-300 shadow-md">
                🛒
            </button>
        </div>
    </div>
</template>