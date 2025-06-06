<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, reactive, watch } from 'vue';
import type { MyPageProps } from '@/types';
import { deepClone } from '@/utils/utils.js';
import dayjs from 'dayjs';


const page = usePage<MyPageProps>();
const links = page.props.links;
const filteredLinks = ref(deepClone(links?.data ?? []));
const isAdmin = page.props.auth.user?.role_name === 'admin';

const loading = ref(false);

/* const filters = reactive({
    search: page.props.filtersLinks?.search ?? '',
    created_at: page.props.filtersLinks?.created_at ?? '',
    order_by: page.props.filtersLinks?.order_by ?? '',
    order_dir: page.props.filtersLinks?.order_dir ?? '',
    page: page.props.filtersLinks?.page ?? 1,
}); */
console.log(filteredLinks.value)
const filters = reactive({
    search: page.props.filtersLinks?.search ?? '',
    date_from: page.props.filtersLinks?.date_from ?? '',
    date_to: page.props.filtersLinks?.date_to ?? '',
    affiliate_id: page.props.filtersLinks?.affiliate_id ?? '',
    order_by: page.props.filtersLinks?.order_by ?? '',
    order_dir: page.props.filtersLinks?.order_dir ?? '',
    page: page.props.filtersLinks?.page ?? 1,
});

function changePage(pageNum: number) {
    filters.page = pageNum;
    updateLinks();
}

function updateLinks() {
    loading.value = true;
    router.get(route('links'), {
        search: filters.search,
        date_from: filters.date_from,
        date_to: filters.date_to,
        affiliate_id: filters.affiliate_id,
        order_by: filters.order_by,
        order_dir: filters.order_dir,
        page: filters.page,
    }, {
        preserveState: true,
        replace: false,
        onFinish: () => {
            loading.value = false;
        }
    });
}

watch(() => page.props.links, (newLinks) => {
    filteredLinks.value = deepClone(newLinks?.data ?? []);
    filters.page = newLinks?.current_page ?? 1;
});

function resetFilters() {
    filters.search = '';
    filters.date_from = '';
    filters.date_to = '';
    filters.order_by = '';
    filters.order_dir = '';
    filters.page = 1;
    updateLinks();
}

function sortBy(field: string) {
    if (filters.order_by === field) {
        filters.order_dir = filters.order_dir === 'asc' ? 'desc' : 'asc';
    } else {
        filters.order_by = field;
        filters.order_dir = 'asc';
    }
    updateLinks();
}


function exportData(format: 'csv' | 'xlsx') {
    const params = {
        ...filters,
        export: format,
    };
    const query = new URLSearchParams(params as any).toString();
    window.open(route('links.export') + '?' + query, '_blank');
}

</script>


<template>
    <AppLayout title="Links">

        <Head title="Links" />

        <div class="p-4 sm:p-6 bg-white dark:bg-[#0A0A0A] transition-colors duration-300">
            <!-- Filtros -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
                <input v-model="filters.search" @input="updateLinks" placeholder="Buscar..."
                    class="input w-full sm:w-1/3 px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200" />

                <!-- Rango de fechas -->
                <input type="date" v-model="filters.date_from" @change="updateLinks"
                    class="input px-4 py-2 border rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                <input type="date" v-model="filters.date_to" @change="updateLinks"
                    class="input px-4 py-2 border rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />

                <!-- Filtro por afiliado (solo admin) -->
                <select v-if="isAdmin" v-model="filters.affiliate_id" @change="updateLinks"
                    class="input px-4 py-2 border rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                    <option value="">Todos los afiliados</option>
                    <option v-for="user in (page.props.affiliates as Array<{ id: number | string; name: string }>)"
                        :key="user.id" :value="user.id">
                        {{ user.name }}
                    </option>
                </select>


                <button @click="resetFilters"
                    class="btn bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition">
                    Reset
                </button>
            </div>

            <div class="overflow-x-auto rounded-lg shadow-lg relative">
                <div v-if="loading"
                    class="absolute inset-0 bg-white/70 dark:bg-[#0A0A0A]/70 flex items-center justify-center z-10">
                    <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                    </svg>
                </div>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-chart-3 text-white dark:bg-chart-1">
                        <tr>
                            <th v-if="isAdmin" @click="sortBy('affiliate_name')"
                                class="px-6 py-3 text-left text-sm font-medium uppercase cursor-pointer hover:underline">
                                Nom Afiliat
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase">Propietat</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase">URL generada</th>
                            <th @click="sortBy('clicks')"
                                class="px-6 py-3 text-left text-sm font-medium uppercase cursor-pointer hover:underline">
                                Clicks
                                <span v-if="filters.order_by === 'clicks'">
                                    {{ filters.order_dir === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th @click="sortBy('conversions')"
                                class="px-6 py-3 text-left text-sm font-medium uppercase cursor-pointer hover:underline">
                                Conversiones
                                <span v-if="filters.order_by === 'conversions'">
                                    {{ filters.order_dir === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th @click="sortBy('total_earned')"
                                class="px-6 py-3 text-left text-sm font-medium uppercase cursor-pointer hover:underline">
                                Ingresos</th>
                                <span v-if="filters.order_by === 'total_earned'">
                                    {{ filters.order_dir === 'asc' ? '↑' : '↓' }}
                                </span>
                            <th @click="sortBy('created_at')"
                                class="px-6 py-3 text-left text-sm font-medium uppercase cursor-pointer hover:underline">
                                Data Creació
                                <span v-if="filters.order_by === 'created_at'">
                                    {{ filters.order_dir === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <transition-group name="fade" tag="tbody">
                        <template v-if="filteredLinks.length">
                            <tr v-for="link in filteredLinks" :key="link.id"
                                class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td v-if="isAdmin" class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                    {{ link.affiliate_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                    {{ link.property_title ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-blue-600 dark:text-blue-400 break-all">
                                    {{ link.generated_url }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                    {{ link.clicks }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                    {{ link.conversions }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-chart-2">
                                    {{ link.total_earned }} €
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                    {{ dayjs(link.created_at).format('DD/MM/YYYY') }}
                                </td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr>
                                <td colspan="6" class="text-center py-6 text-gray-500 dark:text-gray-400">
                                    No hay links para mostrar.
                                </td>
                            </tr>
                        </template>
                    </transition-group>
                </table>
            </div>
            <div class="flex gap-2 mt-4">
                <button @click="exportData('csv')"
                    class="btn text-white hover:bg-gray-600 px-4 py-2 rounded-md text-sm transition">
                    Exportar CSV
                </button>
            </div>

            <!-- Paginación -->
            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <button v-for="pageNum in links?.last_page" :key="pageNum" @click="changePage(pageNum)" :class="[
                    'px-4 py-2 rounded-md transition border',
                    pageNum === page.props.links?.current_page
                        ? 'bg-blue-600 text-white border-blue-700'
                        : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700'
                ]">
                    {{ pageNum }}
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Puedes agregar transiciones o estilos extra aquí si lo deseas */
</style>
