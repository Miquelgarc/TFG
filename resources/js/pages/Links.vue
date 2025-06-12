<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, reactive, watch } from 'vue';
import type { MyPageProps, BreadcrumbItem } from '@/types';
import { deepClone } from '@/utils/utils.js';
import dayjs from 'dayjs';


const page = usePage<MyPageProps>();
const links = page.props.links;
const filteredLinks = ref(deepClone(links?.data ?? []));
const isAdmin = page.props.auth.user?.role_name === 'admin';
const qrUrl = ref('');

const showQR = ref(false);
const selectedQRLink = ref<{ name?: string; generated_url: string } | null>(null);

function generateQR(link: { name?: string; generated_url: string }) {
    selectedQRLink.value = link;
    qrUrl.value = `https://api.qrserver.com/v1/create-qr-code/?data=${encodeURIComponent(link.generated_url)}&size=200x200`;
    showQR.value = true;
}

function closeQR() {
    showQR.value = false;
    selectedQRLink.value = null;
    qrUrl.value = '';
}
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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Links',
        href: '/afiliats/links',
    },
];

</script>


<template>
    <AppLayout :breadcrumbs="breadcrumbs">

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
                    <option value="">Tots els afiliats</option>
                    <option v-for="user in (page.props.affiliates as Array<{ id: number | string; name: string }>)"
                        :key="user.id" :value="user.id">
                        {{ user.name }}
                    </option>
                </select>

                <button @click="router.visit(route('links.create'))"
                    class="bg-chart-2 hover:bg-blue-700 dark:bg-chart-1 text-white px-3 py-2 rounded-lg font-medium">
                    Generar nou link
                </button>
                <button @click="resetFilters"
                    class="btn bg-chart-1 hover:bg-red-600 text-white px-4 py-2 rounded-md transition">
                    Reset
                </button>
            </div>

            <div class="hidden md:block overflow-x-auto rounded-lg shadow-lg relative">
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
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase">Nom link</th>
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
                                Conversions
                                <span v-if="filters.order_by === 'conversions'">
                                    {{ filters.order_dir === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th @click="sortBy('total_earned')"
                                class="px-6 py-3 text-left text-sm font-medium uppercase cursor-pointer hover:underline">
                                Ingresos
                                <span v-if="filters.order_by === 'total_earned'">
                                    {{ filters.order_dir === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>

                            <th @click="sortBy('created_at')"
                                class="px-6 py-3 text-left text-sm font-medium uppercase cursor-pointer hover:underline">
                                Data Creació
                                <span v-if="filters.order_by === 'created_at'">
                                    {{ filters.order_dir === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase">QR</th>

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
                                    {{ link.name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                    {{ link.property_title ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-blue-600 dark:text-blue-400 break-all">
                                    <a :href="link.generated_url">
                                        {{ link.generated_url }}
                                    </a>

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
                                <td class="px-6 py-4">
                                    <button class="text-blue-600 dark:text-blue-400 hover:underline text-sm"
                                        @click="generateQR(link)">
                                        Veure QR
                                    </button>
                                </td>
                                <!-- Modal de QR -->
                                <transition name="fade">
                                    <div v-if="showQR"
                                        class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center">
                                        <div
                                            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl w-[300px] relative text-center">
                                            <button class="absolute top-2 right-3 text-gray-500 hover:text-gray-300"
                                                @click="closeQR">
                                                &times;
                                            </button>
                                            <h2 class="text-lg font-semibold mb-4 dark:text-white">
                                                QR del link
                                                <br>
                                                <span
                                                    class="text-sm font-normal block mt-1 text-gray-500 dark:text-gray-300">
                                                    {{ selectedQRLink?.name || 'Link sense nom' }}
                                                </span>
                                            </h2>
                                            <img :src="qrUrl" alt="QR Code" class="mx-auto mb-4 w-40 h-40" />
                                            <a :href="qrUrl" target="_blank" download
                                                class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-300 dark:hover:text-blue-400 underline">
                                                Descargar QR
                                            </a>
                                        </div>
                                    </div>
                                </transition>
                            </tr>
                        </template>
                        <template v-else>
                            <tr>
                                <td colspan="6" class="text-center py-6 text-gray-500 dark:text-gray-400">
                                    No hi ha links a mostrar.
                                </td>
                            </tr>
                        </template>
                    </transition-group>
                </table>
            </div>
            <!-- Vista móvil simplificada -->
            <div class="md:hidden space-y-4">
                <div v-for="link in filteredLinks" :key="link.id"
                    class="border rounded-lg p-4 bg-white dark:bg-gray-800">
                    <div v-if="isAdmin" class="font-semibold text-gray-700 dark:text-gray-200">{{ link.affiliate_name }}
                    </div>
                    <div><span class="font-semibold">Nom:</span> {{ link.name ?? '—' }}</div>
                    <div><span class="font-semibold">Propietat:</span> {{ link.property_title ?? '—' }}</div>
                    <div><span class="font-semibold">URL:</span>
                        <a :href="link.generated_url"
                            class="text-blue-600 dark:text-blue-300 underline break-all block">{{
                            link.generated_url }}</a>
                    </div>
                    <div><span class="font-semibold">Clicks:</span> {{ link.clicks }}</div>
                    <div><span class="font-semibold">Conversions:</span> {{ link.conversions }}</div>
                    <div><span class="font-semibold">Ingresos:</span> {{ link.total_earned }} €</div>
                    <div><span class="font-semibold">Data:</span> {{ dayjs(link.created_at).format('DD/MM/YYYY') }}
                    </div>
                    <div>
                        <button class="text-blue-600 dark:text-blue-400 underline text-sm"
                            @click="generateQR(link)">Veure
                            QR</button>
                    </div>
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button @click="exportData('csv')"
                    class="btn dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 px-4 py-2 rounded-md text-sm transition">
                    Exportar CSV
                </button>
            </div>
            <div v-if="qrUrl" class="mt-4">
                <img :src="qrUrl" alt="QR Code" class="w-40 h-40" />
                <a :href="qrUrl" target="_blank" download>Descargar QR</a>
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
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
