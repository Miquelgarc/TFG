<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

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
    <AppLayout>

        <Head :title="property.title" />

        <div class="max-w-5xl mx-auto p-6">
            <div class="grid md:grid-cols-3 gap-8 items-start">
                <!-- Columna 1 y 2: Información de la propiedad -->
                <div class="md:col-span-2 space-y-4">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-white">{{ property.title }}</h1>

                    <img :src="property.image_url || 'https://source.unsplash.com/800x400/?house'"
                        alt="Imatge de la casa" class="w-full h-72 object-cover rounded-lg" />

                    <p class="text-gray-700 dark:text-gray-300">{{ property.description }}</p>
                    <p class="text-gray-600 dark:text-gray-400">📍 {{ property.location }}</p>
                    <p class="text-blue-600 dark:text-blue-300 font-semibold text-lg">€{{ property.price_per_night }} /
                        nit</p>
                </div>

                <!-- Columna 3: Formulario de reserva -->
                <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
                    <h2 class="text-xl font-semibold dark:text-white">Reserva la teva estada</h2>

                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-gray-300">Data d'entrada</label>
                        <input type="date" v-model="form.check_in_date" class="w-full px-4 py-2 border rounded-md" />
                        <div v-if="form.errors.check_in_date" class="text-sm text-red-500 mt-1">
                            {{ form.errors.check_in_date }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-gray-300">Data de sortida</label>
                        <input type="date" v-model="form.check_out_date" class="w-full px-4 py-2 border rounded-md" />
                        <div v-if="form.errors.check_out_date" class="text-sm text-red-500 mt-1">
                            {{ form.errors.check_out_date }}
                        </div>
                    </div>

                    <button @click="submitReservation"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-all">
                        Confirmar reserva
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
