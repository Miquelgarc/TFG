<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import type { MyPageProps } from '@/types';

// Chart.js imports
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
} from 'chart.js'
import { Bar } from 'vue-chartjs'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const page = usePage<MyPageProps>();
const user = computed(() => page.props.auth.user ?? null);
const linksTop = computed(() => page.props.linksTop ?? []);


// ✅ Asegúrate de que `comisionesMensuales` venga desde el backend
const comisionesSemanales = computed(() => page.props.comisionesSemanales ?? []);

const totalSemanal = computed(() =>
    comisionesSemanales.value.reduce((sum, item) => sum + Number(item.total), 0)
);


const chartDataSemanal = computed(() => ({
    labels: comisionesSemanales.value.map(item => item.semana_nombre),
    datasets: [
        {
            label: 'Comisiones (€) Semanales',
            data: comisionesSemanales.value.map(item => item.total),
            backgroundColor: '#4f46e5',
        }
    ]
}));


const chartOptions = ref({
    responsive: true,
    plugins: {
        legend: {
            position: 'top' as const,
        },
        title: {
            display: true,
            text: 'Comisiones por mes'
        }
    }
})

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Info afiliat',
        href: '/InfoAfiliat',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Info afiliat" />

        <div class="p-6 space-y-6">
            <div>
                <h1 v-if="user" class="text-2xl font-bold mb-4">{{ user?.company_name }}</h1>
                <p>{{ user?.name }}</p>
                <span>URL: <a class="text-blue-600 underline">{{ user?.website_url }}</a></span>
                <p class="mt-4">Email: {{ user?.email }}</p>
            </div>

            <!-- Gráfico -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Resumen de comisiones</h2>
                <p class="mb-2 font-medium">Total: €{{ totalSemanal.toLocaleString('es-ES', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2 }) }}</p>
                <Bar :data="chartDataSemanal" :options="chartOptions" />
            </div>
        </div>
    </AppLayout>
</template>
