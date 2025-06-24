import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

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
    external?: boolean;
    activePattern?: string;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
};

export interface Role{
    name:string
}
export interface User {
    id: number;
    username: string;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    roles : Role[],
    created_at: string;
    updated_at: string;
}

export interface FlashProps {
    flash?: {
      success?: string
      error?: string
    }
  }

export type BreadcrumbItemType = BreadcrumbItem;
