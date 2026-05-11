<script setup>
    import Nav from '@/Layouts/Nav.vue';
    import { Link, usePage } from '@inertiajs/vue3';
    import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

    const showNav = ref(false);
    const showMobileMenu = ref(false);
    const desktopProfileMenuRef = ref(null);
    const mobileProfileMenuRef = ref(null);
    const dismissAuthNotice = ref(false);
    const page = usePage();
    const guestAvatar = '/images/FotoPerfil.png';
    const authenticatedAvatar = '/images/Render3.png';
    const authenticatedFrame = '/images/Render1.png';
    let authNoticeTimeout = null;

    // These computed values keep the template clean and react to auth changes automatically
    const isAuthenticated = computed(() => Boolean(page.props.auth?.user));
    const authNotice = computed(() => page.props.flash?.authNotice ?? null);
    const resolveAvatarPath = (path) => {
        if (!path) return authenticatedAvatar;
        if (path.startsWith('http://') || path.startsWith('https://')) return path;
        if (path.startsWith('/')) return path;
        if (path.startsWith('storage/')) return `/${path}`;
        return `/storage/${path}`;
    };

    const displayAvatar = computed(() => {
        if (!isAuthenticated.value) {
            return guestAvatar;
        }

        return resolveAvatarPath(page.props.auth?.user?.avatar_path);
    });

    // Reusing the same navigation data avoids repeating links for desktop and mobile
    const navLinks = [
        { label: 'Inicio', href: route('home') },
        { label: 'Cartelera', href: route('billboard') },
        { label: 'Próximos estrenos', href: route('upcoming') },
        { label: 'Sobre nosotros', href: route('about') },
    ];
    const headerLinkClass = 'text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium cursor-pointer';
    const iconButtonClass = 'btn btn-sm btn-ghost text-slate-300 hover:text-yellow-500 transition-colors duration-300';
    const profileButtonClass = 'btn btn-circle btn-outline border-yellow-500 text-yellow-500 hover:bg-yellow-500 hover:text-slate-900 transition-all duration-300 overflow-visible';
    const profileDropdownClass = 'absolute right-0 mt-2 bg-slate-800 border border-yellow-500/20 rounded-lg shadow-xl backdrop-blur-md z-50';
    const desktopAvatarClass = 'pointer-events-none absolute left-1/2 top-1/2 h-[2.85rem] w-[2.85rem] max-w-none -translate-x-1/2 -translate-y-1/2 object-contain';
    const mobileAvatarClass = 'pointer-events-none absolute left-1/2 top-1/2 h-[2.55rem] w-[2.55rem] max-w-none -translate-x-1/2 -translate-y-1/2 object-contain';

    const clearAuthNoticeTimer = () => {
        if (authNoticeTimeout) {
            window.clearTimeout(authNoticeTimeout);
            authNoticeTimeout = null;
        }
    };

    const toggleNav = () => {
        showNav.value = !showNav.value;
    };

    const closeNav = () => {
        showNav.value = false;
    };

    const toggleMobileMenu = () => {
        showMobileMenu.value = !showMobileMenu.value;
    };

    // This checks both profile menu wrappers so the dropdown closes only when clicking outside
    const isClickInsideMenu = (event) => {
        return desktopProfileMenuRef.value?.contains(event.target)
            || mobileProfileMenuRef.value?.contains(event.target);
    };

    const handleOutsideClick = (event) => {
        if (!isClickInsideMenu(event)) {
            closeNav();
        }
    };

    const hideAuthNotice = () => {
        dismissAuthNotice.value = true;
        clearAuthNoticeTimer();
    };

    // When a new flash message arrives, show it again and hide it automatically after a few seconds
    watch(
        authNotice,
        (value) => {
            dismissAuthNotice.value = false;
            clearAuthNoticeTimer();

            if (value) {
                authNoticeTimeout = window.setTimeout(() => {
                    dismissAuthNotice.value = true;
                }, 5000);
            }
        },
        { immediate: true },
    );

    onMounted(() => {
        document.addEventListener('click', handleOutsideClick);
    });

    onBeforeUnmount(() => {
        document.removeEventListener('click', handleOutsideClick);
        clearAuthNoticeTimer();
    });
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
                <template v-for="link in navLinks" :key="link.label">
                    <Link v-if="link.href" :href="link.href" :class="headerLinkClass">{{ link.label }}</Link>
                    <a v-else :class="headerLinkClass">{{ link.label }}</a>
                </template>

                <!-- Dark mode -->
                <button :class="iconButtonClass">
                    🌙
                </button>

                <!-- Perfil -->
                <div ref="desktopProfileMenuRef" class="relative">
                    <button
                        @click.stop="toggleNav"
                        :class="profileButtonClass"
                        :aria-expanded="showNav"
                        aria-haspopup="menu"
                        aria-label="Abrir menu de cuenta"
                    >
                        <span class="relative flex h-9 w-9 items-center justify-center overflow-visible rounded-full">
                            <img
                                :src="displayAvatar"
                                class="h-full w-full rounded-full object-cover"
                                alt="Avatar de usuario"
                            />
                            <img
                                v-if="isAuthenticated"
                                :src="authenticatedFrame"
                                :class="desktopAvatarClass"
                                alt="Marco de perfil"
                            />
                        </span>
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
                            :class="profileDropdownClass"
                            @click.stop
                        >
                            <Nav @close="closeNav" />
                        </div>
                    </Transition>
                </div>

            </div>

            <!-- MOBILE -->
            <div class="flex md:hidden items-center gap-3">
                <button :class="iconButtonClass">🌙</button>

                <!-- Login and register -->
                <div ref="mobileProfileMenuRef" class="relative">
                    <button
                        @click.stop="toggleNav"
                        :class="profileButtonClass"
                        :aria-expanded="showNav"
                        aria-haspopup="menu"
                        aria-label="Abrir menu de cuenta"
                    >
                        <span class="relative flex h-8 w-8 items-center justify-center overflow-visible rounded-full">
                            <img
                                :src="displayAvatar"
                                class="h-full w-full rounded-full object-cover"
                                alt="Avatar de usuario"
                            />
                            <img
                                v-if="isAuthenticated"
                                :src="authenticatedFrame"
                                :class="mobileAvatarClass"
                                alt="Marco de perfil"
                            />
                        </span>
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
                            :class="profileDropdownClass"
                            @click.stop
                        >
                            <Nav @close="closeNav" />
                        </div>
                    </Transition>
                </div>

                <!-- Hamburger for mobile menu -->
                <button @click="toggleMobileMenu" class="text-slate-300 hover:text-yellow-500 transition-colors duration-300">
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
            <template v-for="link in navLinks" :key="`mobile-${link.label}`">
                <Link v-if="link.href" :href="link.href" :class="headerLinkClass">{{ link.label }}</Link>
                <a v-else :class="headerLinkClass">{{ link.label }}</a>
            </template>
        </div>

        <Transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div
                v-if="authNotice && !dismissAuthNotice"
                class="pointer-events-none absolute right-6 top-full z-50 mt-3"
            >
                <div class="pointer-events-auto flex w-[min(26rem,calc(100vw-3rem))] items-start justify-between gap-4 rounded-2xl border border-emerald-400/20 bg-slate-900/95 px-4 py-3 text-sm text-emerald-100 shadow-lg">
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-emerald-400/15 text-emerald-300">✓</span>
                        <p>{{ authNotice }}</p>
                    </div>

                    <button
                        type="button"
                        class="text-emerald-200 transition-colors duration-200 hover:text-white"
                        @click="hideAuthNotice"
                        aria-label="Cerrar aviso"
                    >
                        ✕
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>