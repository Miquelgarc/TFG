<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, reactive, watch } from 'vue';
import type { MyPageProps } from '@/types';
import { deepClone } from '@/utils/utils.js';

const page = usePage<MyPageProps>();
const comisions = page.props.comisions;
const filteredComisions = ref(deepClone(comisions?.data ?? []));
const isAdmin = page.props.auth.user?.role_name === 'admin';
const filters = reactive({
    search: page.props.filtersCommission?.search ?? '',
    generated_at: page.props.filtersCommission?.generated_at ?? '',
    page: page.props.filtersCommission?.page ?? 1,
});

function changePage(pageNum: number) {
    filters.page = pageNum;
    router.get(route('comisions'), {
        search: filters.search,
        date: filters.generated_at,
        page: pageNum,
    }, {
        preserveState: true,
        replace: false,
    });
}

function updateComisions() {
    router.get(route('comisions'), {
        search: filters.search,
        date: filters.generated_at,
    }, {
        preserveState: true,
        replace: false,
    });
}

watch(() => page.props.comisions, (newComs) => {
    filteredComisions.value = deepClone(newComs?.data ?? []);
    filters.page = newComs?.current_page ?? 1;
});

function resetFilters() {
    filters.search = '';
    filters.generated_at = '';
    router.get(route('comisions'), {
        search: '',
        date: '',
    }, {
        preserveState: true,
        replace: true
    });
    updateComisions();
}
</script>


<template>
    <AppLayout title="Comisiones">

        <Head title="Comisiones" />

        <div class="p-4 sm:p-6 bg-white dark:bg-[#0A0A0A] transition-colors duration-300">
            <!-- Filtros -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
                <input v-model="filters.search" @input="updateComisions" placeholder="Buscar..."
                    class="input w-full sm:w-1/3 px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200" />
                <input v-model="filters.generated_at" @change="updateComisions" type="date"
                    class="input px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200" />
                <!-- <button @click="updateComisions"
                    class="btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors duration-200">
                    Buscar
                </button> -->
                <button @click="resetFilters"
                    class="btn bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition-colors duration-200">
                    Reset
                </button>
            </div>

            <!-- Tabla -->
            <div class="overflow-x-auto rounded-lg shadow-lg transition-shadow duration-300">
                <!-- Outer wrapper handles scroll -->
                <div class="transition-all duration-300">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-blue-600 text-white dark:bg-blue-700">
                            <tr>
                                <th v-if="isAdmin" class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">
                                    Nom Afiliat
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">
                                    Descripció
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">
                                    Quantitat
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">
                                    Data
                                </th>
                            </tr>
                        </thead>

                        <!-- Valid use of transition-group in tbody -->
                        <transition-group name="fade" tag="tbody">
                            <template v-if="filteredComisions.length">
                                <tr v-for="c in filteredComisions" :key="c.id"
                                    class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                        {{ c.affiliate_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                        {{ c.description }}
                                    </td>
                                    <td class="px-6 py-4 text-green-600 dark:text-green-400">
                                        €{{ Number(c.amount).toFixed(2) }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                        {{ new Date(c.generated_at).toLocaleDateString() }}
                                    </td>
                                </tr>
                            </template>

                            <template v-else>
                                <tr>
                                    <td colspan="3" class="text-center py-6 text-gray-500 dark:text-gray-400">
                                        No hay comisiones para mostrar.
                                    </td>
                                </tr>
                            </template>
                        </transition-group>

                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <button v-for="pageNum in comisions?.last_page" :key="pageNum" @click="changePage(pageNum)" :class="[
                    'px-4 py-2 rounded-md transition-all duration-200 border',
                    pageNum === page.props.comisions?.current_page
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


</style>