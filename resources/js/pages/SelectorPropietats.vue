<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
  houses: Array<{
    id: number;
    title: string;
    description: string;
    location: string;
    price_per_night: number;
    max_guests: number;
    image_url?: string;
  }>;
}>();

const showForm = ref(false);
const selectedHouse = ref<typeof props.houses[number] | null>(null);
const form = ref<any>(null);

const openForm = (house: typeof props.houses[number]) => {
  selectedHouse.value = house;
  showForm.value = true;
  form.value = useForm({
    property_id: house.id,
    name: '',
  });
};

const closeForm = () => {
  showForm.value = false;
  selectedHouse.value = null;
  form.value.reset();
};

const createLink = () => {
  form.value.post('/affiliate-links', {
    preserveScroll: true,
    onSuccess: () => {
      alert('Link generado exitosamente');
      closeForm();
    },
    onError: () => {
      alert('Error al generar el link');
    },
  });
};
</script>

<template>
  <AppLayout title="Generar Link de Afiliado">
    <Head title="Selecciona una Propiedad" />

    <div class="p-6 max-w-7xl mx-auto">
      <h1 class="text-3xl font-extrabold mb-6 text-gray-800 dark:text-white">
        Selecciona una propiedad para generar un link
      </h1>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="house in props.houses"
          :key="house.id"
          class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl cursor-pointer transition-all duration-300"
          @click="openForm(house)"
        >
          <img
            :src="house.image_url || 'https://source.unsplash.com/600x400/?house,vacation'"
            class="w-full h-48 object-cover"
            alt="Imagen de la propiedad"
          />

          <div class="p-4">
            <h2 class="text-xl font-bold text-blue-700 dark:text-white">{{ house.title }}</h2>
            <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">
              {{ house.description.slice(0, 100) }}...
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">📍 {{ house.location }}</p>
            <p class="text-lg font-semibold text-blue-500 mt-2">
              €{{ house.price_per_night }} / noche
            </p>
          </div>
        </div>
      </div>

      <!-- Modal -->
      <transition name="fade">
        <div
          v-if="showForm"
          class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        >
          <div
            class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl w-full max-w-lg p-6 relative"
          >
            <button
              class="absolute top-2 right-3 text-gray-400 hover:text-gray-200 text-xl"
              @click="closeForm"
            >
              &times;
            </button>

            <h2 class="text-xl font-bold mb-4">
              Crear link para: {{ selectedHouse?.title }}
            </h2>

            <label class="block mb-4">
              <span class="text-gray-700 dark:text-white">Nombre del Link (opcional)</span>
              <input
                v-model="form.name"
                type="text"
                class="mt-1 block w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-white"
                placeholder="Ej: Verano 2025"
              />
            </label>

            <div class="flex justify-end mt-6 space-x-2">
              <button
                class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 rounded"
                @click="closeForm"
              >
                Cancelar
              </button>
              <button
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                @click="createLink"
              >
                Generar Link
              </button>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </AppLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
