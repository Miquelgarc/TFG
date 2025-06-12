<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, watch } from 'vue';
import type { MyPageProps } from '@/types';
import { Bar } from 'vue-chartjs'

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const page = usePage<MyPageProps>();
const user = computed(() => page.props.auth.user ?? null);
const linksTop = computed(() => page.props.linksTop ?? []);
const isDark = ref(false);

const comisionesSemanales = computed(() => page.props.comisionesSemanales ?? []);

const totalSemanal = computed(() => {
    const sum = comisionesSemanales.value.reduce((sum, item) => sum + Number(item.total), 0);
    return parseFloat(sum.toFixed(2));
});

onMounted(() => {
    isDark.value = document.documentElement.classList.contains('dark');

    const observer = new MutationObserver(() => {
        isDark.value = document.documentElement.classList.contains('dark');
    });

    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});

const chartDataSemanal = computed(() => ({
    labels: comisionesSemanales.value.map(item => item.semana_nombre),
    datasets: [
        {
            label: 'Comisiones (€) Setmanals',
            data: comisionesSemanales.value.map(item => item.total),
            backgroundColor: '#4f46e5',
        }
    ]
}));


const chartOptions = ref({});

const updateChartOptions = (dark: boolean) => {
    chartOptions.value = {
        responsive: true,
        plugins: {
            legend: {
                position: 'top' as const,
                labels: {
                    color: dark ? '#ffffff' : '#000000',
                }
            },
            title: {
                display: true,
                text: 'Comisiones por semana',
                color: dark ? '#ffffff' : '#000000',
            },
            tooltip: {
                backgroundColor: dark ? '#1f2937' : '#f9fafb', // gris oscuro o claro
                titleColor: dark ? '#ffffff' : '#000000',
                bodyColor: dark ? '#ffffff' : '#000000',
                borderColor: dark ? '#ffffff22' : '#00000022',
                borderWidth: 1,
            }
        },
        scales: {
            x: {
                ticks: {
                    color: dark ? '#ffffff' : '#000000',
                },
                grid: {
                    color: dark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                }
            },
            y: {
                ticks: {
                    color: dark ? '#ffffff' : '#000000',
                },
                grid: {
                    color: dark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                }
            }
        }
    };
};


watch(isDark, (newVal) => {
    updateChartOptions(newVal);
});

onMounted(() => updateChartOptions(isDark.value));



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

        <div class="p-4 space-y-6">
            <div>
                <h1 v-if="user" class="text-2xl font-bold mb-4">{{ user?.name }}</h1>
                <p class="text-gray-600 dark:text-gray-300">Benvingut a la teva àrea d'afiliat!</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl shadow bg-white dark:bg-gray-800">
                    <h3 class="text-sm text-gray-500 dark:text-gray-300">Nivell d'Afiliat</h3>
                    <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ page.props.stats.nivel }}</p>
                </div>
                <div class="p-4 rounded-xl shadow bg-white dark:bg-gray-800">
                    <h3 class="text-sm text-gray-500 dark:text-gray-300">% Comissió</h3>
                    <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ page.props.stats.porcentaje }}%
                    </p>
                </div>
                <div class="p-4 rounded-xl shadow bg-white dark:bg-gray-800">
                    <h3 class="text-sm text-gray-500 dark:text-gray-300">Total comissions</h3>
                    <p class="text-xl font-semibold text-green-600 dark:text-green-400">€{{
                        page.props.stats.total_comisiones }}</p>
                </div>

                <div class="p-4 rounded-xl shadow bg-white dark:bg-gray-800">
                    <h3 class="text-sm text-gray-500 dark:text-gray-300">Reserves confirmades</h3>
                    <p class="text-xl font-semibold text-blue-600 dark:text-blue-400">{{ page.props.stats.reservas }}
                    </p>
                </div>
                <div class="p-4 rounded-xl shadow bg-white dark:bg-gray-800">
                    <h3 class="text-sm text-gray-500 dark:text-gray-300">Clicks totals rebuts</h3>
                    <p class="text-xl font-semibold text-indigo-600 dark:text-indigo-400">{{ page.props.stats.clicks }}
                    </p>
                </div>
                <div class="p-4 rounded-xl shadow bg-white dark:bg-gray-800">
                    <h3 class="text-sm text-gray-500 dark:text-gray-300">Comissions pendents a cobrar</h3>
                    <p class="text-xl font-semibold text-green-600 dark:text-green-400">€{{
                        page.props.stats.comissions_pendents }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-white dark:bg-transparent rounded-xl shadow p-4">
                    <h2 class="text-lg font-semibold mb-3">Resum de comissions</h2>
                    <p class="mb-2 font-medium">Total: €{{ totalSemanal }}</p>
                    <div class="h-56">
                        <Bar :data="chartDataSemanal" :options="chartOptions" />
                    </div>
                </div>

                <div class="bg-white dark:bg-transparent rounded-xl shadow p-4">
                    <h2 class="text-lg font-semibold mb-3">Links amb más reservas</h2>

                    <ul class="space-y-2">
                        <li v-for="(link, index) in linksTop" :key="index"
                            class="flex items-center text-sm bg-gray-50 dark:bg-gray-800 p-2 mb-3 rounded-xl shadow space-x-3">
                            <span class="font-bold w-5 text-center">{{ index + 1 }}</span>
                            <div class="flex-1">
                                <a :href="link.generated_url" target="_blank"
                                    class="text-blue-800 dark:text-blue-200 underline truncate block">
                                    {{ link.name ?? link.generated_url }}
                                </a>
                                <span class="text-gray-500 dark:text-gray-300 text-xs">Reserves: {{ link.total_reservas
                                }}</span>
                            </div>
                        </li>
                        <button @click="router.visit(route('links'))" class="text-gray-600 text-sm mb-4">
                            Veure tots els links..
                        </button>
                    </ul>
                </div>

            </div>

        </div>
    </AppLayout>
</template>
