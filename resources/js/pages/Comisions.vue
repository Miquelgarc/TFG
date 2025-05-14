<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';
import type { MyPageProps } from '@/types';

const page = usePage<MyPageProps>();
const comisions = page.props.comisions;
/* const user = page.props.user;
 */
const filters = reactive({
    search: page.props.filtersCommission?.search ?? '',
    date: page.props.filtersCommission?.date ?? '',
});

function changePage(pageNum: number) {
    router.get(route('comisions'), {
        search: filters.search,
        date: filters.date,
        page: pageNum
    }, {
        preserveState: true,
        replace: true
    });
}

watch(filters, () => {
    router.get(route('comisions'), {
        search: filters.search,
        date: filters.date
    }, {
        preserveState: true,
        replace: true
    });
}, { deep: true });
</script>

<template>
    <AppLayout title="Comisiones">

        <Head title="Comisiones" />

        <div class="p-4">
            <div class="mb-4 flex gap-4 items-center">
                <input v-model="filters.search" placeholder="Cercar..." class="input" />
                <input v-model="filters.date" type="date" class="input" />
            </div>

            <table class="table-auto w-full border">
                <thead>
                    <tr class="bg-blue-500">
                        <th class="px-4 py-2 text-left">Descripció</th>
                        <th class="px-4 py-2 text-left">Quantitat</th>
                        <th class="px-4 py-2 text-left">Data</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="c in comisions?.data" :key="c.id" class="border-t">
                        <td class="px-4 py-2">{{ c.description }}</td>
                        <td class="px-4 py-2">€{{ Number(c.amount).toFixed(2) }}</td>
                        <td class="px-4 py-2">{{ new Date(c.generated_at).toLocaleDateString() }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Paginación -->
            <div class="mt-4 flex justify-center gap-2">
                <button v-for="pageNum in comisions?.last_page" :key="pageNum" :class="[
                    'px-3 py-1 border rounded',
                    pageNum === comisions?.current_page ? 'bg-blue-500 text-white' : 'bg-white'
                ]" @click="changePage(pageNum)">
                    {{ pageNum }}
                </button>
            </div>
        </div>
    </AppLayout>
</template>
