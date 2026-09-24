export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    role: 'client' | 'realtor' | 'admin';
    phone?: string;
    bio?: string;
}

export type LocalizedFields = Partial<Record<'en' | 'ru' | 'uk', Record<string, string>>>;
export interface PropertyType { id: number; name: string; slug: string; translations?: LocalizedFields; properties_count?: number }
export interface PropertyImage { id: number; path: string; alt?: string }
export interface Property {
    id: number; title: string; slug: string; description: string; translations?: LocalizedFields; operation: 'sale' | 'rent';
    price: string | number; address: string; district: string; rooms?: number; area: string | number;
    floor?: number; total_floors?: number; status: string; is_featured: boolean; cover_url: string;
    type: PropertyType; images: PropertyImage[]; amenities?: {id: number; name: string; translations?: LocalizedFields}[]; realtor?: User;
}
export interface Paginator<T> { data: T[]; current_page: number; last_page: number; total: number; links: {url: string | null; label: string; active: boolean}[] }

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    flash: { success?: string; error?: string };
};
