<script setup>

</script>

<template>
<div class="card bg-base-100 shadow-xl p-6 flex flex-col md:flex-row items-center gap-6">

    <!-- Avatar -->
    <div class="avatar">
        <div class="w-24 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
            <img src="https://i.pravatar.cc/150" />
        </div>
    </div>

    <!-- Info -->
    <div class="flex-1 space-y-2">
        <h2 class="text-2xl font-bold text-primary">{{ user.name }}</h2>
        <p>{{ user.email }}</p>
        <p class="text-sm opacity-70">🎂 {{ user.birth_date }}</p>
    </div>

    <!-- Botón -->
    <button class="btn btn-primary">
        Editar perfil
    </button>

</div>
</template>