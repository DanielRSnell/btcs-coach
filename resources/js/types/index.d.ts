import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface TeamMember {
    employee_number: string;
    employee_name: string;
    first_name: string;
    last_name: string;
    employee_email: string;
    job: string;
    job_code: string;
    org_level_2: string;
    employment_status: string;
    has_account: boolean;
    user_id: number | null;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    role?: string;
    employee_number?: string | null;
    org_level_2?: string | null;
    job?: string | null;
    job_code?: string | null;
    employment_status?: string | null;
    team_members?: TeamMember[];
    pi_behavioral_pattern_id?: number | null;
    pi_behavioral_pattern?: any;
    pi_raw_scores?: any;
    pi_assessed_at?: string | null;
    pi_notes?: string | null;
    pi_chart_image?: string | null;
    has_pi_assessment?: boolean;
    [key: string]: unknown; // This allows for additional properties...
}
