<script setup lang="ts">
import { ref, onMounted } from 'vue'

interface House {
  id: number
  name: string
  description: string
  price: number
}

interface ReservationData {
  start_date: string
  end_date: string
}

const houses = ref<House[]>([])
const selectedHouse = ref<House | null>(null)
const reservationData = ref<ReservationData>({
  start_date: '',
  end_date: '',
})

const fetchHouses = () => {
  fetch('/api/houses')
    .then(res => res.json())
    .then((data: House[]) => {
      houses.value = data
    })
}

const openReservation = (house: House) => {
  selectedHouse.value = house
  reservationData.value = { start_date: '', end_date: '' }
}

const makeReservation = () => {
  if (!selectedHouse.value) return

  const payload = {
    house_id: selectedHouse.value.id,
    start_date: reservationData.value.start_date,
    end_date: reservationData.value.end_date,
  }

  fetch('/api/reservations', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
  })
    .then(res => res.json())
    .then(() => {
      alert('Reserva realitzada correctament!')
      selectedHouse.value = null
    })
    .catch(err => {
      console.error('Error en la reserva:', err)
      alert('Hi ha hagut un error. Torna-ho a intentar.')
    })
}

onMounted(fetchHouses)
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Reserves de Cases</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="house in houses" :key="house.id" class="bg-white shadow rounded-xl p-4">
        <h2 class="text-xl font-semibold mb-2">{{ house.name }}</h2>
        <p class="text-gray-600 mb-2">{{ house.description }}</p>
        <p class="text-sm text-gray-500 mb-2">Preu per nit: {{ house.price }}€</p>
        <button
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          @click="openReservation(house)">
          Reserva ara
        </button>
      </div>
    </div>

    <!-- Modal per reservar -->
    <div v-if="selectedHouse" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h2 class="text-xl font-semibold mb-4">Reserva per: {{ selectedHouse.name }}</h2>

        <label class="block mb-2">
          Data d'entrada
          <input type="date" v-model="reservationData.start_date" class="w-full border rounded px-2 py-1" />
        </label>

        <label class="block mb-2">
          Data de sortida
          <input type="date" v-model="reservationData.end_date" class="w-full border rounded px-2 py-1" />
        </label>

        <button
          class="mt-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
          @click="makeReservation">
          Confirmar reserva
        </button>

        <button
          class="mt-2 text-gray-500 underline text-sm"
          @click="selectedHouse = null">
          Cancel·la
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Estils opcionals */
</style>
