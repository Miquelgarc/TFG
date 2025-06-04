<script setup lang="ts">
import { router } from '@inertiajs/vue3';

function logout() {
    router.post(route('logout'));
}
</script>

<template>
    <div class="min-h-screen bg-white dark:bg-[#0A0A0A] flex items-center justify-center px-4">
        <div class="max-w-xl text-center">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Tu cuenta está pendiente de aprobación
            </h1>

            <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
                Hemos recibido tu solicitud y nuestro equipo la está revisando.
            </p>

            <p class="text-base text-gray-600 dark:text-gray-400 mb-6">
                Te notificaremos por correo cuando tu cuenta sea aprobada. Si tienes dudas, puedes escribirnos.
            </p>

            <button @click="logout" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                Cerrar sesión
            </button>
        </div>
    </div>
</template>
