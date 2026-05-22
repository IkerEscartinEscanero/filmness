<script setup>
    import { useTheme } from '@/utils/useTheme';
    import { Link } from '@inertiajs/vue3';
    import { computed } from 'vue';

    const { isDarkMode } = useTheme();

    const navigationLinks = [
        { label: 'Inicio', href: route('home') },
        { label: 'Cartelera', href: route('billboard') },
        { label: 'Próximos estrenos', href: route('upcoming') },
        { label: 'Sobre nosotros', href: route('about') },
    ];

    const legalLinks = [
        'Aviso legal',
        'Condiciones de compra',
        'Accesibilidad',
        'Política de privacidad',
        'Política de cookies',
    ];

    const footerClass = computed(() => (
        isDarkMode.value
            ? 'bg-slate-900 border-t border-yellow-500/20 text-slate-300'
            : 'bg-slate-100 border-t border-yellow-500/30 text-slate-600'
    ));
    const footerLinkClass = 'hover:text-yellow-500 transition-colors duration-200 cursor-pointer';
    const descriptionClass = computed(() => isDarkMode.value ? 'text-sm text-slate-400 text-center md:text-left leading-relaxed' : 'text-sm text-slate-500 text-center md:text-left leading-relaxed');
    const legalItemClass = computed(() => isDarkMode.value ? 'text-slate-300' : 'text-slate-500');
    const socialIconClass = computed(() => isDarkMode.value ? 'social-icon-link text-slate-400 hover:text-yellow-500 transition-colors duration-200' : 'social-icon-link text-slate-500 hover:text-yellow-500 transition-colors duration-200');
    const copyrightClass = computed(() => isDarkMode.value ? 'text-sm text-slate-500' : 'text-sm text-slate-400');
</script>

<template>
    <footer :class="footerClass">
        <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-3 gap-10 items-center">
            <!-- Navegation -->
            <div class="flex flex-col items-center md:items-start gap-2 text-sm">
                <h6 class="text-yellow-500 font-semibold uppercase tracking-wider mb-2">Navegación</h6>
                <Link
                    v-for="link in navigationLinks"
                    :key="link.label"
                    :href="link.href"
                    :class="footerLinkClass"
                >
                    {{ link.label }}
                </Link>
            </div>

            <!-- Logo and description -->
            <div class="flex flex-col items-center gap-2 text-sm">
                <div class="flex items-center gap-3">
                    <img src="/images/Logo.png" class="w-16 h-16" />
                    <span class="text-yellow-500 font-bold text-2xl tracking-wide">FilmNess</span>
                </div>
                <p :class="descriptionClass">
                    Tu plataforma de cine favorita. Descubre películas, reserva entradas y disfruta de la mejor experiencia cinematográfica.
                </p>
            </div>

            <!-- Legal -->
            <div class="flex flex-col items-center md:items-end gap-2 text-sm">
                <h6 class="text-yellow-500 font-semibold uppercase tracking-wider mb-2">Legal</h6>
                <span
                    v-for="link in legalLinks"
                    :key="link"
                    :class="legalItemClass"
                >
                    {{ link }}
                </span>
            </div>
        </div>

        <!-- Social media -->
        <div class="border-t border-yellow-500/10 py-6 flex flex-col items-center gap-4">
            <div class="flex gap-5">
                <!-- Instagram -->
                <a href="https://www.instagram.com/_.ikeree05._/?hl=es" :class="socialIconClass">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
                <!-- X -->
                <a href="https://x.com/snoowbaaall" :class="socialIconClass">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.262 5.638L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
                <!-- TikTok -->
                <a href="https://www.tiktok.com/@_.bolitadenieve._?lang=es" :class="socialIconClass">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.84 1.56V6.78a4.85 4.85 0 01-1.07-.09z"/>
                    </svg>
                </a>
                <!-- LinkedIn -->
                <a href="https://www.linkedin.com/in/iker-escart%C3%ADn-0259b6310/" :class="socialIconClass">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                </a>
                <!-- YouTube -->
                <a href="https://www.youtube.com/@SnowBall938" :class="socialIconClass">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/>
                    </svg>
                </a>
                <!-- GitHub -->
                <a href="https://github.com/IkerEscartinEscanero" :class="socialIconClass">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
                    </svg>
                </a>
            </div>

            <!-- COPYRIGHT -->
            <p :class="copyrightClass">© 2026 FilmNess. Todos los derechos reservados.</p>
        </div>
    </footer>
</template>