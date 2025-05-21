<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: "Configuració estil",
        href: '/settings/appearance',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Configuració estil" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Configuració estil" description="Canvia aqui el tipus d'estil que més t'agradi" />
                <AppearanceTabs />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
