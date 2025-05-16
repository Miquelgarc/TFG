<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, ChartColumnBig, ClipboardList, Cog, Folder, House, LayoutDashboard, Users } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';
import type { MyPageProps } from '@/types';

const user = computed(() => usePage<MyPageProps>().props.auth.user);
const role = computed(() => user.value?.role_name);

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [

        {
            title: 'COMISIONS',
            href: '/afiliats/comisions',
            icon: ChartColumnBig,
        }
        , {
            title: 'CONFIGURACIÓ',
            href: '/settings',
            icon: Cog,
        }


    ];

    if (role.value === 'admin') {
        items.unshift({
            title: 'AFILIATS',
            href: '/afiliats',
            icon: Users,
        });
    }
    if (role.value === 'affiliate') {
        items.unshift({
            title: 'LINKS AFILIAT',
            href: '/afiliats/links',
            icon: ClipboardList,
        });
        items.unshift({
            title: 'INFO AFILIAT',
            href: '/info-afiliat',
            icon: LayoutDashboard,
        });
        items.push({
            title: 'RESERVES',
            href: '/reserva',
            icon: House,
        });
    }

    return items;
});


const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/Miquelgarc/TFG',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://docs.google.com/document/d/12TDuZGa8HpOofdo7Ke2HgM39qQ0rzigxD7-j07BLu7g/edit?tab=t.0#heading=h.t04qy2hhnb3t',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
