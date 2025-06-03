<script setup lang="ts">
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

// eslint-disable-next-line @typescript-eslint/no-unused-vars
const props = defineProps<{
  houses: Array<{
    id: number
    title: string
    description: string
    location: string
    price_per_night: number
    max_guests: number
    is_available: boolean
    image_url?: string
  } | null>
}>()

const showReservationForm = ref(false)
const selectedHouse = ref<typeof props.houses[number] | null>(null)
const showDetails = ref(false)
const form = ref<any>(null)

const openHouseDetails = (house: typeof props.houses[number]) => {
  selectedHouse.value = house
  showDetails.value = true

  form.value = useForm({
    property_id: house?.id ?? null,
    check_in_date: '',
    check_out_date: '',
    url: window.location.href,
  })
}

const closeDetails = () => {
  showDetails.value = false
  setTimeout(() => {
    selectedHouse.value = null
    showReservationForm.value = false
    form.value.reset()
  }, 300)
}

const makeReservation = () => {
  form.value.post('/reserva', {
    preserveScroll: true,
    onSuccess: () => {
      showReservationForm.value = false
      showDetails.value = false
      selectedHouse.value = null
      form.value.reset()
      alert('Reserva feta correctament!')
    },
    onError: () => {
      alert('Hi ha hagut un error.')
    },
  })
}
</script>

<template>
  <AppLayout title="Reserves">

    <Head title="Reserves" />

    <div class="p-6 max-w-7xl mx-auto">
      <h1 class="text-3xl font-extrabold mb-6 text-[#374151] dark:text-white">Cases disponibles per reservar</h1>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div v-for="house in houses.filter(h => h !== null)" :key="house!.id"
          class="bg-white dark:bg-[#374151] rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 cursor-pointer"
          @click="openHouseDetails(house!)">
          <img :src="house.image_url || 'https://source.unsplash.com/600x400/?house,vacation'" alt="Imatge de la casa"
            class="w-full h-48 object-cover" />

          <div class="p-4 flex flex-col justify-between h-full">
            <div>
              <h2 class="text-xl font-semibold text-[#1E3A8A] dark:text-white">{{ house.title }}</h2>
              <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">{{ house.description.slice(0, 100) }}...</p>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">📍 {{ house.location }}</p>
              <p class="text-lg font-medium text-[#0EA5E9] mt-2">€{{ house.price_per_night }} / nit</p>
            </div>

            <button class="mt-4 bg-[#0EA5E9] hover:bg-[#1E3A8A] text-white py-2 rounded-xl text-sm font-medium">
              Més informació
            </button>
          </div>
        </div>
      </div>

      <!-- Detall de la casa -->
      <transition name="fade">
        <div v-if="selectedHouse"
          class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
          <div
            class="bg-white dark:bg-[#374151] text-gray-800 dark:text-white rounded-2xl w-full max-w-2xl p-6 relative animate-fade transition-opacity duration-300"
            :class="{ 'opacity-0': !showDetails }">
            <button class="absolute top-2 right-3 text-gray-400 hover:text-gray-200 text-xl"
              @click="closeDetails">&times;</button>

            <img :src="selectedHouse.image_url || 'https://source.unsplash.com/800x400/?house'" alt="Imatge de la casa"
              class="w-full h-56 object-cover rounded-lg mb-4" />

            <h2 class="text-2xl font-bold mb-2">{{ selectedHouse.title }}</h2>
            <p class="mb-2">{{ selectedHouse.description }}</p>
            <p><strong>Ubicació:</strong> {{ selectedHouse.location }}</p>
            <p><strong>Preu:</strong> €{{ selectedHouse.price_per_night }} / nit</p>
            <p><strong>Capacitat màxima:</strong> {{ selectedHouse.max_guests }} hostes</p>

            <div class="flex justify-end space-x-2 pt-4">
              <button v-if="!showReservationForm"
                class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-white px-4 py-2 rounded-lg"
                @click="closeDetails">
                Tancar
              </button>

              <button v-if="!showReservationForm"
                class="bg-[#A3E635] hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium"
                @click="showReservationForm = true">
                Reserva ara
              </button>
            </div>

            <!-- Formulari de reserva -->
            <div v-if="showReservationForm" class="mt-6 border-t pt-4 space-y-4">
              <h3 class="text-xl font-semibold text-[#1E3A8A] dark:text-white">Reserva la teva estada</h3>
              <label>
                <span class="text-gray-700 dark:text-white mb-2 px-2 py-4">Data d'entrada</span>
                <input type="date" v-model="form.check_in_date"
                  class="mt-2 mb-1 block w-full border rounded px-3 py-2 dark:text-white dark:fill-white" />
              </label>
              <span v-if="form.errors.check_in_date" class="text-red-500 text-sm block mt-1">
                {{ form.errors.check_in_date }}
              </span>

              <label>
                <span class="text-gray-700 dark:text-white px-2">Data de sortida</span>
                <input type="date" v-model="form.check_out_date"
                  class="mt-2 block w-full border rounded px-3 py-2 dark:text-white dark:bg-[#374151]" />
              </label>
              <span v-if="form.errors.check_out_date" class="text-red-500 text-sm block mt-1">
                {{ form.errors.check_out_date }}
              </span>


              <div class="flex justify-end space-x-2 mt-4">
                <button
                  class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-white px-4 py-2 rounded-lg"
                  @click="showReservationForm = false">Cancel·la</button>
                <button class="bg-[#1E3A8A] hover:bg-[#1e4d8a] text-white px-4 py-2 rounded"
                  @click="makeReservation">Confirmar
                  reserva</button>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </AppLayout>
</template>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }

  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-fade {
  animation: fadeIn 0.3s ease-out;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

:root {
  --marian: #1E3A8A;
  --piction: #0EA5E9;
  --beige: #F5F5DC;
  --spring: #A3E635;
  --charcoal: #374151;
  --white: #FFFFFF;
}

.text-marian {
  color: var(--marian);
}

.text-piction {
  color: var(--piction);
}

.text-charcoal {
  color: var(--charcoal);
}

.bg-piction {
  background-color: var(--piction);
}

.bg-marian {
  background-color: var(--marian);
}

.bg-spring {
  background-color: var(--spring);
}

.bg-charcoal {
  background-color: var(--charcoal);
}
</style>
