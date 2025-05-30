<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { onMounted, onBeforeUnmount, ref, watch, reactive } from 'vue';
import type { AfiliadoPagination, MyPageProps, BreadcrumbItem } from '@/types';
import Swal from 'sweetalert2';
import type { SweetAlertResult } from 'sweetalert2';
import axios from 'axios';
import { toast } from 'vue3-toastify';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Afiliats',
        href: '/afiliats',
    },
];

const page = usePage<MyPageProps>();

const afiliates = page.props.afiliates as AfiliadoPagination;
import { computed } from 'vue';

const filteredList = computed(() => {
    return afiliates.data.filter((afiliat: any) => {
        const matchesSearch =
            !filters.search ||
            afiliat.name.toLowerCase().includes(filters.search.toLowerCase()) ||
            afiliat.email.toLowerCase().includes(filters.search.toLowerCase()) ||
            afiliat.company_name.toLowerCase().includes(filters.search.toLowerCase());

        const matchesStatus =
            !filters.status || afiliat.status === filters.status;

        return matchesSearch && matchesStatus;
    });
});


const filters = reactive({
    search: page.props.filters?.search ?? '',
    status: page.props.filters?.status ?? ''
})


// Cambiar estado
const dropdownVisibleId = ref<number | null>(null)
const dropdownRef = ref<HTMLElement | null>(null);


const toggleDropdown = (id: number) => {
    dropdownVisibleId.value = dropdownVisibleId.value === id ? null : id
}



const handleClickOutside = (event: MouseEvent) => {
    const dropdownEl = dropdownRef.value as HTMLElement | null;
    if (
        dropdownEl &&
        typeof dropdownEl.contains === 'function' &&
        !dropdownEl.contains(event.target as Node)
    ) {
        dropdownVisibleId.value = null;
    }

};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});


// Editar (redirige a vista de edición)
const editAfiliat = (afiliat: any) => {
    router.visit(`/afiliats/${afiliat.id}/editar`)
}

// Eliminar con confirmación
const deleteAfiliat = (afiliat: any) => {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
    }).then((result: SweetAlertResult) => {
        if (result.isConfirmed) {
            router.delete(`/afiliats/${afiliat.id}`, {
                preserveScroll: true,
            })
        }
    })
}

watch(() => filters.status, (newVal) => {
    console.log('Nuevo status:', newVal);
});



const changeStatus = async (id: number, status: string) => {
    try {
        await axios.post(`/afiliats/${id}/cambiar-estat`, { status });

        const afiliat = afiliates.data.find(a => a.id === id)
        if (afiliat) afiliat.status = status

        dropdownVisibleId.value = null
        toast.success('Estado actualizado correctamente');

        // No need to assign to filteredList.value; it will update automatically as a computed property.

    } catch (error) {
        toast.error('Error al cambiar el estado');
        console.error(error);
    }
};

function changePage(pageNum: number) {
    router.get(
        route('afiliats'),
        { search: filters.search, status: filters.status, page: pageNum },
        { preserveState: true, replace: true }
    );
}


</script>

<template>

    <Head title="Afiliats" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 bg-white dark:bg-[#0A0A0A] transition-colors duration-300">
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-4">

                <input v-model="filters.search" type="text" placeholder="Cercar Afiliat..."
                    class="input w-full sm:w-1/3 px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200" />

                <select v-model="filters.status"
                    class="input px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200">
                    <option value="">Tots</option>
                    <option value="active">Actiu</option>
                    <option value="pending">Pendent</option>
                    <option value="rejected">Inactiu</option>
                </select>

            </div>

            <div class="overflow-x-auto rounded-lg shadow-lg transition-shadow duration-300">
                <div class="transition-all duration-300">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-blue-600 text-white dark:bg-blue-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Nom</th>
                                <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Empresa
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Estat</th>
                                <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Accions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="afiliat in filteredList" :key="afiliat.id"
                                class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ afiliat.name }}</td>
                                <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ afiliat.email }}</td>
                                <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ afiliat.company_name }}</td>
                                
                                <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                    <span @click="toggleDropdown(afiliat.id)"
                                        class="cursor-pointer relative inline-block uppercase tracking-wide px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-800': afiliat.status === 'active',
                                            'bg-gray-200 text-gray-700': afiliat.status === 'pending',
                                            'bg-red-100 text-red-800': afiliat.status === 'rejected',
                                        }">
                                        {{ afiliat.status }}
                                    </span>
                                    <transition name="fade-scale">
                                        <div v-if="dropdownVisibleId === afiliat.id" ref="dropdownRef"
                                            class="absolute z-10 mt-1 w-36 bg-white border border-gray-200 rounded-lg shadow-xl text-sm text-gray-800">
                                            <ul class="divide-y divide-gray-100">
                                                <li v-for="option in ['active', 'pending', 'rejected']" :key="option"
                                                    @click="changeStatus(afiliat.id, option)"
                                                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer flex items-center justify-between"
                                                    :class="{
                                                        'font-semibold text-green-600': option === 'active',
                                                        'font-semibold text-gray-500': option === 'pending',
                                                        'font-semibold text-red-500': option === 'rejected'
                                                    }">
                                                    <span>{{ option }}</span>
                                                    <span v-if="afiliat.status === option"
                                                        class="text-xs text-gray-400">✓</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </transition>

                                </td>

                                <td class="px-4 py-2 flex gap-2">
                                    <button @click="editAfiliat(afiliat)" class="dark:text-white px-3 py-1 rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>

                                    </button>
                                    <button @click="deleteAfiliat(afiliat)"
                                        class="text-chart-5 px-3 py-1 rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>

                                    </button>
                                </td>
                            </tr>

                            <tr v-if="afiliates.data.length === 0">
                                <td colspan="6" class="text-center px-4 py-6 text-gray-500">
                                    No se encontraron afiliados.
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>

            <div class="flex justify-center pt-4">
                <button v-for="page in afiliates.last_page" :key="page" :class="[
                    'mx-1 px-3 py-1 rounded-lg border',
                    page === afiliates.current_page
                        ? 'bg-blue-500 text-white'
                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'
                ]" @click="changePage(page)">
                    {{ page }}
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
input:focus,
select:focus {
    outline: none;
    outline: 2px solid blue;
}

.fade-scale-enter-active,
.fade-scale-leave-active {
    transition: all 0.15s ease;
}

.fade-scale-enter-from {
    opacity: 0;
    transform: scale(0.95);
}

.fade-scale-leave-to {
    opacity: 0;
    transform: scale(0.95);
}
</style>
