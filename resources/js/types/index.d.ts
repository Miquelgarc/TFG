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
    comisions?: Pagination<Commission>;
    afiliates?: Pagination<Afiliado>;
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
    name: string;
    amount: number;
    description: string;
    generated_at: string;
    
}

interface Filters {
    search?: string;
    status?: string;
}
interface FiltersCommission {
    search?: string;
    date?: string;
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
