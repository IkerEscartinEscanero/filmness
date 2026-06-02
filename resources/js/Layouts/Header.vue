<script setup>
    import Nav from '@/Layouts/Nav.vue';
    import { useTheme } from '@/utils/useTheme';
    import { Link, usePage } from '@inertiajs/vue3';
    import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

    const showNav = ref(false);
    const showMobileMenu = ref(false);
    const desktopProfileMenuRef = ref(null);
    const mobileProfileMenuRef = ref(null);
    const dismissAuthNotice = ref(false);
    const dismissFlashNotice = ref(false);
    const { isDarkMode, initializeTheme, toggleTheme } = useTheme();
    const page = usePage();
    const guestAvatar = '/images/FotoPerfil.png';
    const authenticatedAvatar = '/images/Render3.png';
    const authenticatedFrame = '/images/Render1.png';
    let authNoticeTimeout = null;
    let flashNoticeTimeout = null;

    // These computed values keep the template clean and react to auth changes automatically
    const isAuthenticated = computed(() => Boolean(page.props.auth?.user));
    const authNotice = computed(() => page.props.flash?.authNotice ?? null);
    const flashSuccess = computed(() => page.props.flash?.success ?? null);
    const flashError = computed(() => page.props.flash?.error ?? null);
    const flashNotice = computed(() => flashError.value ?? flashSuccess.value ?? null);
    const flashNoticeType = computed(() => (flashError.value ? 'error' : 'success'));
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
    const headerWrapperClass = computed(() => (
        isDarkMode.value
            ? 'sticky top-0 z-50 bg-slate-900 backdrop-blur-md border-b border-yellow-500/20 shadow-lg transition-all duration-300'
            : 'sticky top-0 z-50 bg-slate-100 backdrop-blur-md border-b border-yellow-500/30 shadow-md transition-all duration-300'
    ));
    const logoTextClass = computed(() => (
        'text-yellow-500 font-bold text-2xl md:text-3xl tracking-wide'
    ));
    const headerLinkClass = computed(() => (
        isDarkMode.value
            ? 'text-slate-300 hover:text-yellow-500 transition-colors duration-300 font-medium cursor-pointer'
            : 'text-slate-600 hover:text-yellow-500 transition-colors duration-300 font-medium cursor-pointer'
    ));
    const iconButtonClass = computed(() => (
        isDarkMode.value
            ? 'btn btn-sm btn-ghost text-slate-300 hover:text-yellow-500 transition-colors duration-300'
            : 'btn btn-sm btn-ghost text-slate-500 hover:text-yellow-500 transition-colors duration-300'
    ));
    const profileButtonClass = computed(() => (
        'btn btn-circle btn-outline border-yellow-500 text-yellow-500 hover:bg-yellow-500 hover:text-slate-900 transition-all duration-300 overflow-visible'
    ));
    const profileDropdownClass = computed(() => (
        isDarkMode.value
            ? 'absolute right-0 mt-2 bg-slate-800 border border-yellow-500/20 rounded-lg shadow-xl backdrop-blur-md z-50'
            : 'absolute right-0 mt-2 bg-white border border-yellow-500/20 rounded-lg shadow-xl backdrop-blur-md z-50'
    ));
    const mobileMenuClass = computed(() => (
        isDarkMode.value
            ? 'md:hidden border-t border-yellow-500/20 px-6 py-4 flex flex-col gap-4 text-sm'
            : 'md:hidden border-t border-yellow-500/20 px-6 py-4 flex flex-col gap-4 text-sm bg-slate-100'
    ));
    const authNoticeWrapperClass = computed(() => (
        isDarkMode.value
            ? 'pointer-events-auto flex w-[min(26rem,calc(100vw-3rem))] items-start justify-between gap-4 rounded-2xl border border-emerald-400/20 bg-slate-900/95 px-4 py-3 text-sm text-emerald-100 shadow-lg'
            : 'pointer-events-auto flex w-[min(26rem,calc(100vw-3rem))] items-start justify-between gap-4 rounded-2xl border border-emerald-500/30 bg-white/95 px-4 py-3 text-sm text-emerald-800 shadow-lg'
    ));
    const authNoticeBadgeClass = computed(() => (
        isDarkMode.value
            ? 'mt-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-emerald-400/15 text-emerald-300'
            : 'mt-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-700'
    ));
    const authNoticeCloseButtonClass = computed(() => (
        isDarkMode.value
            ? 'text-emerald-200 transition-colors duration-200 hover:text-white'
            : 'text-emerald-600 transition-colors duration-200 hover:text-emerald-900'
    ));
    const flashNoticeWrapperClass = computed(() => {
        if (flashNoticeType.value === 'error') {
            return isDarkMode.value
                ? 'pointer-events-auto flex w-[min(28rem,calc(100vw-3rem))] items-start justify-between gap-4 rounded-2xl border border-red-400/30 bg-slate-900/95 px-4 py-3 text-sm text-red-100 shadow-lg'
                : 'pointer-events-auto flex w-[min(28rem,calc(100vw-3rem))] items-start justify-between gap-4 rounded-2xl border border-red-500/35 bg-white/95 px-4 py-3 text-sm text-red-800 shadow-lg';
        }

        return isDarkMode.value
            ? 'pointer-events-auto flex w-[min(28rem,calc(100vw-3rem))] items-start justify-between gap-4 rounded-2xl border border-emerald-400/20 bg-slate-900/95 px-4 py-3 text-sm text-emerald-100 shadow-lg'
            : 'pointer-events-auto flex w-[min(28rem,calc(100vw-3rem))] items-start justify-between gap-4 rounded-2xl border border-emerald-500/30 bg-white/95 px-4 py-3 text-sm text-emerald-800 shadow-lg';
    });
    const flashNoticeBadgeClass = computed(() => {
        if (flashNoticeType.value === 'error') {
            return isDarkMode.value
                ? 'mt-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-red-400/20 text-red-300'
                : 'mt-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-red-100 text-red-700';
        }

        return isDarkMode.value
            ? 'mt-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-emerald-400/15 text-emerald-300'
            : 'mt-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-700';
    });
    const flashNoticeCloseButtonClass = computed(() => {
        if (flashNoticeType.value === 'error') {
            return isDarkMode.value
                ? 'text-red-200 transition-colors duration-200 hover:text-white'
                : 'text-red-600 transition-colors duration-200 hover:text-red-900';
        }

        return isDarkMode.value
            ? 'text-emerald-200 transition-colors duration-200 hover:text-white'
            : 'text-emerald-600 transition-colors duration-200 hover:text-emerald-900';
    });
    const desktopAvatarClass = 'pointer-events-none absolute left-1/2 top-1/2 h-[2.85rem] w-[2.85rem] max-w-none -translate-x-1/2 -translate-y-1/2 object-contain';
    const mobileAvatarClass = 'pointer-events-none absolute left-1/2 top-1/2 h-[2.55rem] w-[2.55rem] max-w-none -translate-x-1/2 -translate-y-1/2 object-contain';

    const clearAuthNoticeTimer = () => {
        if (authNoticeTimeout) {
            window.clearTimeout(authNoticeTimeout);
            authNoticeTimeout = null;
        }
    };

    const clearFlashNoticeTimer = () => {
        if (flashNoticeTimeout) {
            window.clearTimeout(flashNoticeTimeout);
            flashNoticeTimeout = null;
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

    const hideFlashNotice = () => {
        dismissFlashNotice.value = true;
        clearFlashNoticeTimer();
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

    watch(
        flashNotice,
        (value) => {
            dismissFlashNotice.value = false;
            clearFlashNoticeTimer();

            if (value) {
                flashNoticeTimeout = window.setTimeout(() => {
                    dismissFlashNotice.value = true;
                }, 5000);
            }
        },
        { immediate: true },
    );

    onMounted(() => {
        initializeTheme();
        document.addEventListener('click', handleOutsideClick);
    });

    onBeforeUnmount(() => {
        document.removeEventListener('click', handleOutsideClick);
        clearAuthNoticeTimer();
        clearFlashNoticeTimer();
    });
</script>

<template>
    <div :class="headerWrapperClass">
        <div class="px-6 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-3">
                <Link :href="route('home')">
                    <img src="/images/Logo.png" class="w-16 h-16 md:w-24 md:h-24 transition-transform duration-300 hover:scale-110" />
                </Link>
                <Link :href="route('home')" :class="logoTextClass">FilmNess</Link>
            </div>

            <!-- Principal header in desktop -->
            <div class="hidden md:flex gap-6 items-center text-sm">
                <template v-for="link in navLinks" :key="link.label">
                    <Link v-if="link.href" :href="link.href" :class="headerLinkClass">{{ link.label }}</Link>
                    <a v-else :class="headerLinkClass">{{ link.label }}</a>
                </template>

                <!-- Dark mode -->
                <button
                    type="button"
                    :class="iconButtonClass"
                    :aria-label="isDarkMode ? 'Activar modo claro' : 'Activar modo oscuro'"
                    @click="toggleTheme"
                >
                    <svg
                        v-if="isDarkMode"
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        viewBox="0 0 40 40"
                        aria-hidden="true"
                    >
                        <path fill="#ffeea3" d="M17.845,33.57l-0.248-0.043c-0.764-0.135-1.53-0.34-2.276-0.612l-0.236-0.085l-4.025,2.657 l0.288-4.804l-0.191-0.162c-0.604-0.508-1.167-1.072-1.676-1.676l-0.162-0.192l-4.804,0.288l2.657-4.024L7.085,24.68 c-0.271-0.748-0.477-1.513-0.611-2.276L6.43,22.156L2.118,20l4.312-2.156l0.044-0.248c0.135-0.763,0.341-1.529,0.611-2.277 l0.086-0.236l-2.657-4.024l4.805,0.288l0.162-0.192c0.507-0.603,1.07-1.167,1.675-1.675l0.191-0.162l-0.288-4.804l4.025,2.657 l0.235-0.085c0.749-0.271,1.516-0.478,2.277-0.612l0.248-0.043L20,2.118l2.155,4.312l0.248,0.043 c0.762,0.134,1.528,0.34,2.277,0.612l0.235,0.085l4.025-2.657l-0.288,4.804l0.191,0.162c0.604,0.509,1.168,1.072,1.675,1.675 l0.162,0.192l4.805-0.288l-2.657,4.024l0.086,0.236c0.271,0.748,0.477,1.514,0.611,2.277l0.044,0.248L37.882,20l-4.312,2.156 l-0.044,0.248c-0.135,0.763-0.341,1.529-0.611,2.276l-0.086,0.236l2.657,4.024l-4.804-0.288l-0.162,0.192 c-0.509,0.604-1.072,1.168-1.676,1.676l-0.191,0.162l0.288,4.804l-4.025-2.657l-0.236,0.085c-0.746,0.271-1.513,0.477-2.276,0.612 l-0.248,0.043L20,37.882L17.845,33.57z" />
                        <path fill="#ba9b48" d="M20,3.236l1.596,3.192l0.225,0.45l0.495,0.088c0.736,0.13,1.474,0.328,2.193,0.589l0.473,0.171 l0.419-0.277l2.98-1.968l-0.213,3.555l-0.03,0.502l0.384,0.324c0.582,0.49,1.125,1.033,1.615,1.615l0.324,0.384l0.501-0.03 l3.555-0.213l-1.968,2.98l-0.277,0.419l0.171,0.473c0.261,0.719,0.459,1.457,0.589,2.193l0.087,0.495l0.45,0.225L36.764,20 l-3.192,1.596l-0.45,0.225l-0.087,0.495c-0.13,0.736-0.328,1.474-0.589,2.193l-0.171,0.472l0.277,0.419l1.968,2.98l-3.555-0.213 l-0.502-0.03l-0.324,0.384c-0.489,0.582-1.033,1.125-1.615,1.615l-0.384,0.324l0.03,0.501l0.213,3.555l-2.98-1.968l-0.419-0.277 l-0.473,0.171c-0.719,0.261-1.457,0.459-2.193,0.589l-0.495,0.087l-0.225,0.45L20,36.764l-1.596-3.192l-0.225-0.45l-0.495-0.087 c-0.736-0.13-1.474-0.328-2.193-0.589l-0.473-0.171l-0.419,0.277l-2.98,1.968l0.213-3.555l0.03-0.501l-0.384-0.324 c-0.582-0.49-1.126-1.033-1.615-1.615l-0.324-0.384l-0.502,0.03l-3.555,0.213l1.968-2.98l0.277-0.419L7.555,24.51 c-0.261-0.719-0.459-1.457-0.589-2.193l-0.087-0.495l-0.45-0.225L3.236,20l3.192-1.596l0.45-0.225l0.087-0.495 c0.13-0.736,0.328-1.474,0.589-2.193l0.171-0.473L7.45,14.598l-1.968-2.98l3.555,0.213l0.501,0.03l0.324-0.384 c0.49-0.582,1.033-1.125,1.615-1.615l0.384-0.324l-0.03-0.502l-0.213-3.555l2.98,1.968l0.419,0.277l0.473-0.171 c0.719-0.261,1.457-0.459,2.193-0.589l0.495-0.088l0.225-0.45L20,3.236 M20,1l-2.491,4.981c-0.814,0.144-1.601,0.358-2.36,0.634 L10.5,3.545l0.333,5.552c-0.627,0.528-1.208,1.109-1.736,1.736L3.546,10.5l3.07,4.649c-0.276,0.76-0.49,1.547-0.634,2.36L1,20 l4.981,2.491c0.144,0.814,0.358,1.601,0.634,2.36L3.546,29.5l5.552-0.333c0.528,0.627,1.109,1.208,1.736,1.736L10.5,36.454 l4.649-3.07c0.76,0.276,1.547,0.49,2.36,0.634L20,39l2.491-4.981c0.814-0.144,1.601-0.358,2.36-0.634l4.649,3.07l-0.333-5.552 c0.627-0.528,1.208-1.109,1.736-1.736l5.552,0.333l-3.07-4.649c0.276-0.76,0.49-1.547,0.634-2.36L39,20l-4.981-2.491 c-0.144-0.814-0.358-1.601-0.634-2.36l3.07-4.649l-5.552,0.333c-0.528-0.627-1.109-1.208-1.736-1.736L29.5,3.545l-4.649,3.07 c-0.76-0.275-1.547-0.49-2.36-0.634L20,1L20,1z" />
                        <path fill="#f5ce85" d="M20 10A10 10 0 1 0 20 30A10 10 0 1 0 20 10Z" />
                    </svg>
                    <img
                        v-else
                        width="20"
                        height="20"
                        src="https://img.icons8.com/office/100/new-moon.png"
                        alt="new-moon"
                    />
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
                <button
                    type="button"
                    :class="iconButtonClass"
                    :aria-label="isDarkMode ? 'Activar modo claro' : 'Activar modo oscuro'"
                    @click="toggleTheme"
                >
                    <svg
                        v-if="isDarkMode"
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        viewBox="0 0 40 40"
                        aria-hidden="true"
                    >
                        <path fill="#ffeea3" d="M17.845,33.57l-0.248-0.043c-0.764-0.135-1.53-0.34-2.276-0.612l-0.236-0.085l-4.025,2.657 l0.288-4.804l-0.191-0.162c-0.604-0.508-1.167-1.072-1.676-1.676l-0.162-0.192l-4.804,0.288l2.657-4.024L7.085,24.68 c-0.271-0.748-0.477-1.513-0.611-2.276L6.43,22.156L2.118,20l4.312-2.156l0.044-0.248c0.135-0.763,0.341-1.529,0.611-2.277 l0.086-0.236l-2.657-4.024l4.805,0.288l0.162-0.192c0.507-0.603,1.07-1.167,1.675-1.675l0.191-0.162l-0.288-4.804l4.025,2.657 l0.235-0.085c0.749-0.271,1.516-0.478,2.277-0.612l0.248-0.043L20,2.118l2.155,4.312l0.248,0.043 c0.762,0.134,1.528,0.34,2.277,0.612l0.235,0.085l4.025-2.657l-0.288,4.804l0.191,0.162c0.604,0.509,1.168,1.072,1.675,1.675 l0.162,0.192l4.805-0.288l-2.657,4.024l0.086,0.236c0.271,0.748,0.477,1.514,0.611,2.277l0.044,0.248L37.882,20l-4.312,2.156 l-0.044,0.248c-0.135,0.763-0.341,1.529-0.611,2.276l-0.086,0.236l2.657,4.024l-4.804-0.288l-0.162,0.192 c-0.509,0.604-1.072,1.168-1.676,1.676l-0.191,0.162l0.288,4.804l-4.025-2.657l-0.236,0.085c-0.746,0.271-1.513,0.477-2.276,0.612 l-0.248,0.043L20,37.882L17.845,33.57z" />
                        <path fill="#ba9b48" d="M20,3.236l1.596,3.192l0.225,0.45l0.495,0.088c0.736,0.13,1.474,0.328,2.193,0.589l0.473,0.171 l0.419-0.277l2.98-1.968l-0.213,3.555l-0.03,0.502l0.384,0.324c0.582,0.49,1.125,1.033,1.615,1.615l0.324,0.384l0.501-0.03 l3.555-0.213l-1.968,2.98l-0.277,0.419l0.171,0.473c0.261,0.719,0.459,1.457,0.589,2.193l0.087,0.495l0.45,0.225L36.764,20 l-3.192,1.596l-0.45,0.225l-0.087,0.495c-0.13,0.736-0.328,1.474-0.589,2.193l-0.171,0.472l0.277,0.419l1.968,2.98l-3.555-0.213 l-0.502-0.03l-0.324,0.384c-0.489,0.582-1.033,1.125-1.615,1.615l-0.384,0.324l0.03,0.501l0.213,3.555l-2.98-1.968l-0.419-0.277 l-0.473,0.171c-0.719,0.261-1.457,0.459-2.193,0.589l-0.495,0.087l-0.225,0.45L20,36.764l-1.596-3.192l-0.225-0.45l-0.495-0.087 c-0.736-0.13-1.474-0.328-2.193-0.589l-0.473-0.171l-0.419,0.277l-2.98,1.968l0.213-3.555l0.03-0.501l-0.384-0.324 c-0.582-0.49-1.126-1.033-1.615-1.615l-0.324-0.384l-0.502,0.03l-3.555,0.213l1.968-2.98l0.277-0.419L7.555,24.51 c-0.261-0.719-0.459-1.457-0.589-2.193l-0.087-0.495l-0.45-0.225L3.236,20l3.192-1.596l0.45-0.225l0.087-0.495 c0.13-0.736,0.328-1.474,0.589-2.193l0.171-0.473L7.45,14.598l-1.968-2.98l3.555,0.213l0.501,0.03l0.324-0.384 c0.49-0.582,1.033-1.125,1.615-1.615l0.384-0.324l-0.03-0.502l-0.213-3.555l2.98,1.968l0.419,0.277l0.473-0.171 c0.719-0.261,1.457-0.459,2.193-0.589l0.495-0.088l0.225-0.45L20,3.236 M20,1l-2.491,4.981c-0.814,0.144-1.601,0.358-2.36,0.634 L10.5,3.545l0.333,5.552c-0.627,0.528-1.208,1.109-1.736,1.736L3.546,10.5l3.07,4.649c-0.276,0.76-0.49,1.547-0.634,2.36L1,20 l4.981,2.491c0.144,0.814,0.358,1.601,0.634,2.36L3.546,29.5l5.552-0.333c0.528,0.627,1.109,1.208,1.736,1.736L10.5,36.454 l4.649-3.07c0.76,0.276,1.547,0.49,2.36,0.634L20,39l2.491-4.981c0.814-0.144,1.601-0.358,2.36-0.634l4.649,3.07l-0.333-5.552 c0.627-0.528,1.208-1.109,1.736-1.736l5.552,0.333l-3.07-4.649c0.276-0.76,0.49-1.547,0.634-2.36L39,20l-4.981-2.491 c-0.144-0.814-0.358-1.601-0.634-2.36l3.07-4.649l-5.552,0.333c-0.528-0.627-1.109-1.208-1.736-1.736L29.5,3.545l-4.649,3.07 c-0.76-0.275-1.547-0.49-2.36-0.634L20,1L20,1z" />
                        <path fill="#f5ce85" d="M20 10A10 10 0 1 0 20 30A10 10 0 1 0 20 10Z" />
                    </svg>
                    <img
                        v-else
                        width="20"
                        height="20"
                        src="https://img.icons8.com/office/100/new-moon.png"
                        alt="new-moon"
                    />
                </button>

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
        <div v-if="showMobileMenu" :class="mobileMenuClass">
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
                <div :class="authNoticeWrapperClass">
                    <div class="flex items-start gap-3">
                        <span :class="authNoticeBadgeClass">✓</span>
                        <p>{{ authNotice }}</p>
                    </div>

                    <button
                        type="button"
                        :class="authNoticeCloseButtonClass"
                        @click="hideAuthNotice"
                        aria-label="Cerrar aviso"
                    >
                        ✕
                    </button>
                </div>
            </div>
        </Transition>

        <Transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div
                v-if="flashNotice && !dismissFlashNotice"
                class="pointer-events-none absolute right-6 top-full z-50 mt-3"
            >
                <div :class="flashNoticeWrapperClass">
                    <div class="flex items-start gap-3">
                        <span :class="flashNoticeBadgeClass">{{ flashNoticeType === 'error' ? '!' : '✓' }}</span>
                        <p>{{ flashNotice }}</p>
                    </div>

                    <button
                        type="button"
                        :class="flashNoticeCloseButtonClass"
                        @click="hideFlashNotice"
                        aria-label="Cerrar notificacion"
                    >
                        ✕
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>