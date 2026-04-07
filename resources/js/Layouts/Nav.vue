<script setup>
    import { Link, usePage } from '@inertiajs/vue3';

    const page = usePage();

    defineProps({
        canLogin: {
            type: Boolean,
        },
        canRegister: {
            type: Boolean,
        },
        laravelVersion: {
            type: String,
            required: true,
        },
        phpVersion: {
            type: String,
            required: true,
        },
    });
</script>

<template>
    <div class="flex flex-col space-y-2 p-4">
        <Link
            v-if="$page.props.auth.user"
            :href="route('logout')"
            method="post"
            class="block px-4 py-2 text-white hover:text-yellow-500 hover:bg-slate-700 rounded-md transition-colors duration-300"
        >
            Log out
        </Link>

        <template v-else>
            <Link
                :href="route('login')"
                class="block px-4 py-2 text-white hover:text-yellow-500 hover:bg-slate-700 rounded-md transition-colors duration-300"
            >
                Log in
            </Link>

            <Link
                v-if="page.props.canRegister"
                :href="route('register')"
                class="block px-4 py-2 text-white hover:text-yellow-500 hover:bg-slate-700 rounded-md transition-colors duration-300"
            >
                Register
            </Link>
        </template>
    </div>
</template>