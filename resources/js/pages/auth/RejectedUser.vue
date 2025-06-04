<script setup lang="ts">
import { router } from '@inertiajs/vue3';

function logout() {
  router.post(route('logout'));
}
</script>

<template>
  <div class="min-h-screen bg-white dark:bg-[#0A0A0A] flex items-center justify-center px-4">
    <div class="max-w-xl text-center">
      <h1 class="text-3xl sm:text-4xl font-bold text-red-600 dark:text-red-400 mb-4">
        Tu cuenta ha sido rechazada
      </h1>

      <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
        Lamentablemente, tu solicitud de registro no ha sido aprobada.
      </p>

      <p class="text-base text-gray-600 dark:text-gray-400 mb-6">
        Si crees que esto fue un error, puedes contactar con el equipo de soporte para obtener más información.
      </p>

      <button @click="logout" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
        Cerrar sesión
      </button>
    </div>
  </div>
</template>
