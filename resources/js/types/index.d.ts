import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

// Interfaces para la estructura de tu aplicación
export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
}

// Estructura del usuario autenticado
export interface AuthUser {
    id: number;
    name: string;
    email: string;
    role_name: 'admin' | 'affiliate' | null;
    role: string | null;
    company_name: string;
    website_url: string;
}

// Aquí tipas correctamente los props que Inertia pasa al componente
export interface MyPageProps extends PageProps {
    filters?: Filters;
    filtersCommission?: FiltersCommission;
    links?: Pagination<Links>;
    filtersLinks?: FiltersLinks;
    filtersReseras?: FiltersReservas;
    reservas?: Pagination<Reservas>;
    comisions?: Pagination<Commission>;
    afiliates?: Pagination<Afiliado>;
    comisionesMensuales: Comision[];
    comisionesSemanales: ComisionSemanal[];
    linksTop: LinksTop[];

    auth: {
        user: AuthUser | null;
    };
    [key: string]: unknown;
}

// Estructura de un usuario
export interface User {
    id: number;
    name: string;
    email: string;
    company_name: string;
    website_url: string;

}
interface Afiliado {
    id: number;
    name: string;
    email: string;
    company_name: string;
    website_url: string;
    status: string;
}
interface Commission {
    id: number;
    amount: number;
    description: string;
    generated_at: string;
}

interface Comision {
    mes: string;
    total: number;
}

interface ComisionSemanal {
    semana: number;
    semana_nombre: string;
    total: number;
}
interface Links {
    target_url: string;
    generated_url: string;
    clicks: string;
    conversions: string;
    created_at: string;
}

interface LinksTop {
    url: string;
    clicks: number;
    conversions: number;
}

interface Reservas {
    property_name: string;
    afiliate_link: string;
    afiliate_name: string;
    check_in: string;
    check_out: string;
    total: string;
    commission: string;
    status: string;
    created_at: string;
}

interface FiltersReservas {
    search?: string;
    date_from?: string;
    date_to?: string;
    order_by?: string;
    order_dir?: string;
    affiliate_id?: number;
    page?: number;
    status?: string;
}

interface Filters {
    search?: string;
    status?: string;
}
interface FiltersCommission {
    search?: string;
    date_from?: string;
    date_to?: string;
    order_by?: string;
    order_dir?: string;
    affiliate_id?: number;
    page?: number;
}
interface FiltersLinks {
    search?: string;
    created_at?: string;
    order_by?: string;
    order_dir?: string;
    page?: number;
    date_from?: string;
    date_to?: string;
    affiliate_id?: number;
}
interface Pagination<T> {
    data: T[];
    total: number;
    per_page: number;
    current_page: number;
    last_page: number;
}

interface AfiliadoPagination {
    data: Afiliado[];
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
}



export type BreadcrumbItemType = BreadcrumbItem;
