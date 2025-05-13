<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue'
import type { MyPageProps } from '@/types'; 

/* import PlaceholderPattern from '../components/PlaceholderPattern.vue';
 */
const page = usePage<MyPageProps>();
const user = computed(() => page.props.auth.user ?? null);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Info afiliat',
        href: '/InfoAfiliat',
    },
];

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 v-if="user" class="text-2xl font-bold mb-4">{{ user?.company_name }}</h1>
            <p>{{ user?.name }}</p>
            <span>URL:  <a>{{ user?.website_url }}</a></span>
            <p class="mt-4">Email: {{ user?.email }}</p>

        </div>
    </AppLayout>
</template>
