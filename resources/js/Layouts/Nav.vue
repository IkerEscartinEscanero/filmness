<script setup>
    import { Link, usePage } from '@inertiajs/vue3';
    import { computed } from 'vue';

    const emit = defineEmits(['close']);

    const page = usePage();
    const isAuthenticated = computed(() => Boolean(page.props.auth?.user));
    const menuLinkClass = 'block whitespace-nowrap rounded-md px-3 py-2 text-left text-white transition-colors duration-300 hover:bg-slate-700 hover:text-yellow-500';

    // The menu content changes depending on whether the user is authenticated or not, so I compute it based on the auth state and available routes
    const menuItems = computed(() => {
        if (isAuthenticated.value) {
            return [
                { label: 'Mi perfil', href: route('profile.edit') },
                { label: 'Log out', href: route('logout'), method: 'post' },
            ];
        }

        const items = [
            { label: 'Log in', href: route('login') },
        ];

        if (page.props.canRegister) {
            items.push({ label: 'Register', href: route('register') });
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