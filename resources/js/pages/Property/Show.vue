<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  property: {
    id: number
    title: string
    description: string
    location: string
    price_per_night: number
    max_guests: number
    image_url?: string
  }
  affiliate_link_id?: number | null
}>()

const form = useForm({
  property_id: props.property.id,
  check_in_date: '',
  check_out_date: '',
  affiliate_link_id: props.affiliate_link_id ?? null,
})

const submitReservation = () => {
  form.post('/reserva', {
    onSuccess: () => {
      alert('Reserva creada exitosament')
      form.reset()
    },
    onError: () => {
      console.error('Error al crear la reserva', form.errors)
    },
  })
}
</script>

<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white px-4 py-10">
    <div class="max-w-6xl mx-auto space-y-10">

      <!-- Título global y posible CTA -->
      <div class="text-center">
        <h1 class="text-4xl font-bold">{{ property.title }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Reserva fàcilment el teu allotjament ideal</p>
      </div>

      <!-- Contenido principal -->
      <div class="grid md:grid-cols-3 gap-10 items-start">
        <!-- Información de la propiedad -->
        <div class="md:col-span-2 space-y-6 bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
          <img
            :src="property.image_url || 'https://source.unsplash.com/800x400/?house'"
            alt="Imatge de la casa"
            class="w-full h-80 object-cover rounded-lg"
          />

          <div class="space-y-2">
            <p class="text-lg">{{ property.description }}</p>

            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
              <span>📍 {{ property.location }}</span>
              <span>• 👥 {{ property.max_guests }} hostes</span>
            </div>

            <p class="text-blue-600 font-semibold text-xl">€{{ property.price_per_night }} / nit</p>
          </div>
        </div>

        <!-- Formulario de reserva -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md space-y-5">
          <h2 class="text-xl font-semibold">Reserva la teva estada</h2>

          <div>
            <label class="block text-sm font-medium mb-1">Data d'entrada</label>
            <input
              type="date"
              v-model="form.check_in_date"
              class="w-full px-4 py-2 border rounded-md"
            />
            <div v-if="form.errors.check_in_date" class="text-sm text-red-500 mt-1">
              {{ form.errors.check_in_date }}
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Data de sortida</label>
            <input
              type="date"
              v-model="form.check_out_date"
              class="w-full px-4 py-2 border rounded-md"
            />
            <div v-if="form.errors.check_out_date" class="text-sm text-red-500 mt-1">
              {{ form.errors.check_out_date }}
            </div>
          </div>

          <button
            @click="submitReservation"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition"
          >
            Confirmar reserva
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
