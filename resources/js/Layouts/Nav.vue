<script setup>
    import { Link, usePage } from '@inertiajs/vue3';
    import { computed } from 'vue';

    const emit = defineEmits(['close']);

    const page = usePage();
    const isAuthenticated = computed(() => Boolean(page.props.auth?.user));
    const isAdmin = computed(() => page.props.auth?.user?.role === 'admin');
    const menuLinkClass = 'block whitespace-nowrap rounded-md px-3 py-2 text-left text-white transition-colors duration-300 hover:bg-slate-700 hover:text-yellow-500';

    // El menú cambia según si hay sesión iniciada o no.
    const menuItems = computed(() => {
        if (isAuthenticated.value) {
            const items = [];
            
            if (isAdmin.value) {
                items.push({ label: 'Panel administrativo', href: route('admin.dashboard') });
            }
            
            items.push({ label: 'Mi perfil', href: route('profile.edit') });
            items.push({ label: 'Cerrar sesión', href: route('logout'), method: 'post' });
            
            return items;
        }

        const items = [
            { label: 'Iniciar sesión', href: route('login') },
        ];

        if (page.props.canRegister) {
            items.push({ label: 'Registrarse', href: route('register') });
        }

        return items;
    });

    const closeMenu = () => emit('close');
</script>

<template>
    <div class="flex w-max min-w-0 flex-col space-y-1 p-2">
        <Link
            v-for="item in menuItems"
            :key="item.label"
            :href="item.href"
            :method="item.method"
            :class="menuLinkClass"
            @click="closeMenu"
        >
            {{ item.label }}
        </Link>
    </div>
</template>