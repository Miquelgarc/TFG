<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { onMounted, onBeforeUnmount, ref, watch, reactive } from 'vue';
import type { AfiliadoPagination, MyPageProps } from '@/types';
import Swal from 'sweetalert2';
import type { SweetAlertResult } from 'sweetalert2';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import { deepClone } from '@/utils/utils.js';

const page = usePage<MyPageProps>();

const afiliates = page.props.afiliates as AfiliadoPagination;
const filteredList = ref(deepClone(afiliates.data));

const filters = reactive({
    search: page.props.filters?.search ?? '',
    status: page.props.filters?.status ?? ''
})


// Cambiar estado
const dropdownVisible = ref<number | null>(null)
const dropdownRefs = reactive<{ [key: number]: HTMLElement | null }>({});


const toggleDropdown = (id: number) => {
    dropdownVisible.value = dropdownVisible.value === id ? null : id
}
const changeStatus = async (id: number, status: string) => {
    try {
        await axios.post(`/afiliats/${id}/cambiar-estat`, { status });
        const afiliat = afiliates.data.find(a => a.id === id)
        if (afiliat) afiliat.status = status
        dropdownVisible.value = null
        toast.success('Estado actualizado correctamente');
        router.get(route('afiliats'), {
            search: filters.search,
            status: filters.status,
        }, {
            preserveState: true,
            replace: true,
        });

    } catch (error) {
        toast.error('Error al cambiar el estado');
        console.error(error);
    }
};
const handleClickOutside = (event: MouseEvent) => {
    const clickedInsideAny = Object.values(dropdownRefs).some(el => {
        return el && el.contains(event.target as Node);
    });

    if (!clickedInsideAny) {
        dropdownVisible.value = null;
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

// Filtre
watch(filters, (newFilters) => {
    filteredList.value = afiliates.data.filter((afiliat: any) => {
        const matchesSearch =
            !newFilters.search ||
            afiliat.name.toLowerCase().includes(newFilters.search.toLowerCase()) ||
            afiliat.email.toLowerCase().includes(newFilters.search.toLowerCase()) ||
            afiliat.company_name.toLowerCase().includes(newFilters.search.toLowerCase());

        const matchesStatus =
            !newFilters.status || afiliat.status === newFilters.status;

        return matchesSearch && matchesStatus;
    });
}, { deep: true });



function changePage(pageNum: number) {
    router.get(
        route('afiliats'),
        { search: filters.search, status: filters.status, page: pageNum },
        { preserveState: true, replace: true }
    );
}


</script>

<template>

    <Head title="Afiliados" />

    <AppLayout>
        <div class="p-6 space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex gap-2 w-full md:w-auto">
                    <input v-model="filters.search" type="text" placeholder="Buscar afiliado..."
                        class="w-full md:w-64 px-4 py-2 rounded-lg border border-gray-300 focus:ring focus:ring-blue-200" />

                    <select v-model="filters.status"
                        class="px-3 py-2 rounded-lg border bg-background border-gray-300 focus:ring focus:ring-gray-200">
                        <option value="">Todos</option>
                        <option value="active">Activo</option>
                        <option value="pending">Pendiente</option>
                        <option value="rejected">Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="text-gray-600 border-b">
                        <tr>
                            <th class="px-4 py-2">Nombre</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Empresa</th>
                            <th class="px-4 py-2">Website</th>
                            <th class="px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="afiliat in filteredList" :key="afiliat.id" class="hover:bg-gray-100 transition">
                            <td class="px-4 py-2 text-black">{{ afiliat.name }}</td>
                            <td class="px-4 py-2 text-black">{{ afiliat.email }}</td>
                            <td class="px-4 py-2 text-black">{{ afiliat.company_name }}</td>
                            <td class="px-4 py-2">
                                <a :href="afiliat.website_url" class="text-blue-600 hover:underline" target="_blank"
                                    rel="noopener">
                                    {{ afiliat.website_url }}
                                </a>
                            </td>
                            <td class="px-4 py-2 w-32">
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
                                    <div v-if="dropdownVisible === afiliat.id"
                                        :ref="el => dropdownRefs[afiliat.id] = el as HTMLElement"
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
                                <button @click="editAfiliat(afiliat)"
                                    class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded">
                                    Editar
                                </button>
                                <button @click="deleteAfiliat(afiliat)"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                    Eliminar
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
