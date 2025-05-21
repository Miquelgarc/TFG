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

const filters = reactive({
    search: page.props.filtersLinks?.search ?? '',
    created_at: page.props.filtersLinks?.created_at ?? '',
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
        date: filters.created_at,
        order_by: filters.order_by,
        order_dir: filters.order_dir,
        page: filters.page,
    }, {
        preserveState: true,
        replace: true,
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
    filters.created_at = '';
    filters.order_by = '';
    filters.order_dir = '';
    filters.page = 1;
    updateLinks();
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

                <input v-model="filters.created_at" @change="updateLinks" type="date"
                    class="input px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200" />

                <select v-model="filters.order_by" @change="updateLinks"
                    class="input px-2 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                    <option disabled value="">Ordenar por</option>
                    <option value="clicks">Clicks</option>
                    <option value="conversions">Conversiones</option>
                </select>

                <select v-model="filters.order_dir" @change="updateLinks"
                    class="input px-2 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                    <option disabled value="">Dirección</option>
                    <option value="asc">Ascendente</option>
                    <option value="desc">Descendente</option>
                </select>

                <button @click="resetFilters"
                    class="btn bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition-colors duration-200">
                    Reset
                </button>
            </div>


            <!-- Tabla -->
            <div class="overflow-x-auto rounded-lg shadow-lg transition-shadow duration-300">
                <div class="transition-all duration-300">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-blue-600 text-white dark:bg-blue-700">
                            <tr>
                                <th v-if="isAdmin" class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Nom Afiliat
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">URL pare
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">URL
                                    generada</th>
                                <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Clicks</th>
                                <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Conversions
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Data
                                    Creació
                                </th>
                            </tr>
                        </thead>
                        <transition-group name="fade" tag="tbody">
                            <template v-if="filteredLinks.length">
                                <tr v-for="link in filteredLinks" :key="link.id"
                                    class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <td v-if="isAdmin" class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ link.affiliate_name }}</td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ link.target_url }}</td>
                                    <td class="px-6 py-4 text-blue-600 dark:text-blue-400 break-all">{{
                                        link.generated_url }}</td>
                                    <td class="px-6 py-4 text-gray-700 dark:text-gray-200">{{ link.clicks }}</td>
                                    <td class="px-6 py-4 text-green-600 dark:text-green-400">{{ link.conversions }}</td>
                                    <td class="px-6 py-4 text-green-600 dark:text-green-400"> {{
                                        dayjs(link.created_at).format('DD/MM/YYYY') }}
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr>
                                    <td colspan="4" class="text-center py-6 text-gray-500 dark:text-gray-400">
                                        No hay links para mostrar.
                                    </td>
                                </tr>
                            </template>
                        </transition-group>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <button v-for="pageNum in links?.last_page" :key="pageNum" @click="changePage(pageNum)" :class="[
                    'px-4 py-2 rounded-md transition-all duration-200 border',
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
