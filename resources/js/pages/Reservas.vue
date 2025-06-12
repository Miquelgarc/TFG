<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, reactive, watch } from 'vue';
import type { MyPageProps, BreadcrumbItem } from '@/types';
import { deepClone } from '@/utils/utils';

const page = usePage<MyPageProps>();
const reservations = page.props.reservas;
const filteredReservations = ref(deepClone(reservations?.data ?? []));
const loading = ref(false);
const filters = reactive({
    search: page.props.filtersReseras?.search ?? '',
    status: page.props.filtersReseras?.status ?? '',
    date_from: page.props.filtersReseras?.date_from ?? '',
    date_to: page.props.filtersReseras?.date_to ?? '',
    page: page.props.filtersReseras?.page ?? 1,
});

function changePage(pageNum: number) {
    filters.page = pageNum;
    updateReservations();
}

function updateReservations() {
    loading.value = true;
    router.get(route('reservations'), { ...filters }, {
        preserveState: true,
        replace: false,
        onFinish: () => loading.value = false,
    });
}

function resetFilters() {
    filters.search = '';
    filters.status = '';
    filters.date_from = '';
    filters.date_to = '';
    filters.page = 1;
    updateReservations();
}

watch(() => page.props.reservas, (newData) => {
    filteredReservations.value = deepClone(newData?.data ?? []);
    filters.page = newData?.current_page ?? 1;
});
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reserves',
        href: '/reserves',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Reserves" />

        <div class="p-4 sm:p-6 bg-white dark:bg-[#0A0A0A] transition-colors duration-300">
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
                <input v-model="filters.search" @input="updateReservations" placeholder="Buscar..."
                    class="input w-full sm:w-1/3 px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200" />

                <input type="date" v-model="filters.date_from" @change="updateReservations"
                    class="input px-4 py-2 border rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />

                <input type="date" v-model="filters.date_to" @change="updateReservations"
                    class="input px-4 py-2 border rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />

                <select v-model="filters.status" @change="updateReservations"
                    class="input px-4 py-2 border rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                    <option value="">Totss els estats</option>
                    <option value="charged">Pendents</option>
                    <option value="confirmed">Condirmades</option>
                    <option value="cancelled">Cancel·lades</option>
                </select>

                <button @click="resetFilters"
                    class="btn bg-destructive hover:bg-red-600 text-white px-4 py-2 rounded-md transition-colors duration-200">
                    Reset
                </button>
            </div>

            <!-- Tabla para escritorio -->
            <div class="hidden md:block overflow-x-auto rounded-lg shadow-lg transition-shadow duration-300">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 shadow-sm">
                    <thead class="bg-chart-3 text-white dark:bg-chart-1">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Propietat</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Entrada</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Sortida</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Preu Total</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Estat</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Link afiliat</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Comissió generada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-if="filteredReservations.length">
                            <tr v-for="r in filteredReservations" :key="r.id"
                                class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                    {{ r.property?.title ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                    {{ new Date(r.check_in_date).toLocaleDateString() }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                    {{ new Date(r.check_out_date).toLocaleDateString() }}
                                </td>
                                <td class="px-6 py-4 text-chart-2 font-bold">€{{ Number(r.total_price).toFixed(2) }}</td>
                                <td class="px-6 py-4">
                                    <span :class="{
                                        'text-yellow-600': r.status === 'pending',
                                        'text-green-600': r.status === 'confirmed',
                                        'text-red-600': r.status === 'cancelled',
                                    }">
                                        {{ r.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                    <span v-if="r.affiliate_link">
                                        {{ r.affiliate_link.name || r.affiliate_link.generated_url }}
                                    </span>
                                    <span v-else class="italic text-gray-500">Reserva directa</span>
                                </td>
                                <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                    <span v-if="r.commissions?.length">
                                        €{{ r.commissions.reduce((total, c) => total + parseFloat(c.amount), 0).toFixed(2) }}
                                    </span>
                                    <span v-else class="italic text-gray-500">—</span>
                                </td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr>
                                <td colspan="7" class="text-center py-6 text-gray-500 dark:text-gray-400">
                                    No hi ha reserves per mostrar.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Vista móvil -->
            <div class="md:hidden space-y-4">
                <div v-if="filteredReservations.length" v-for="r in filteredReservations" :key="r.id"
                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow">
                    <div><strong>Propietat:</strong> {{ r.property?.title ?? 'N/A' }}</div>
                    <div><strong>Entrada:</strong> {{ new Date(r.check_in_date).toLocaleDateString() }}</div>
                    <div><strong>Sortida:</strong> {{ new Date(r.check_out_date).toLocaleDateString() }}</div>
                    <div><strong>Preu Total:</strong> €{{ Number(r.total_price).toFixed(2) }}</div>
                    <div><strong>Estat:</strong>
                        <span :class="{
                            'text-yellow-600': r.status === 'pending',
                            'text-green-600': r.status === 'confirmed',
                            'text-red-600': r.status === 'cancelled',
                        }">
                            {{ r.status }}
                        </span>
                    </div>
                    <div><strong>Link afiliat:</strong>
                        <span v-if="r.affiliate_link">
                            {{ r.affiliate_link.name || r.affiliate_link.generated_url }}
                        </span>
                        <span v-else class="italic text-gray-500">Reserva directa</span>
                    </div>
                    <div><strong>Comissió generada:</strong>
                        <span v-if="r.commissions?.length">
                            €{{ r.commissions.reduce((total, c) => total + parseFloat(c.amount), 0).toFixed(2) }}
                        </span>
                        <span v-else class="italic text-gray-500">—</span>
                    </div>
                </div>
                <div v-else class="text-center py-6 text-gray-500 dark:text-gray-400">
                    No hi ha reserves per mostrar.
                </div>
            </div>

            <div class="mt-6 flex justify-center gap-2">
                <button v-for="pageNum in reservations?.last_page" :key="pageNum" @click="changePage(pageNum)"
                    :class="[
                        'px-4 py-2 rounded-md',
                        filters.page === pageNum ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-white'
                    ]">
                    {{ pageNum }}
                </button>
            </div>
        </div>
    </AppLayout>
</template>
