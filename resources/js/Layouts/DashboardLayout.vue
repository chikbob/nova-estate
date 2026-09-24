<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from './AppLayout.vue';
import type { PageProps } from '@/types';
import { useLocale } from '@/composables/useLocale';
import DashboardIcon from '@/Components/DashboardIcon.vue';
const page = usePage<PageProps>();
const user = page.props.auth.user!;
const { t } = useLocale();
const roleLabel = () => t(`common.${user.role}`);
const active = (path:string) => page.url.startsWith(path);
const linkClass = (path:string) => ['group flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-bold transition', active(path) ? 'bg-[#174c43] text-white shadow-lg shadow-[#174c43]/15' : 'text-stone-600 hover:bg-stone-100 hover:text-[#174c43]'];
</script>
<template>
    <AppLayout><div class="container-page grid gap-8 py-10 lg:grid-cols-[250px_1fr]">
        <aside class="h-fit overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-[0_16px_40px_rgba(31,50,44,.06)] lg:sticky lg:top-28">
            <div class="border-b border-stone-100 bg-[#f2eee5] p-5"><div class="flex items-center gap-3"><div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-[#174c43] text-base font-extrabold text-white">{{user.name.charAt(0)}}</div><div class="min-w-0"><div class="truncate font-bold text-stone-800">{{ user.name }}</div><div class="mt-0.5 text-[10px] font-extrabold uppercase tracking-[.14em] text-[#8b6b49]">{{ roleLabel() }}</div></div></div></div>
            <nav class="grid gap-1 p-3">
                <Link href="/dashboard" :class="linkClass('/dashboard')"><DashboardIcon name="overview"/>{{t('dashboard.overview')}}</Link>
                <Link v-if="user.role==='client'" href="/favorites" :class="linkClass('/favorites')"><DashboardIcon name="heart"/>{{t('dashboard.favorites')}}</Link>
                <Link :href="user.role==='client'?'/my-applications':'/manage/applications'" :class="linkClass(user.role==='client'?'/my-applications':'/manage/applications')"><DashboardIcon name="clock"/>{{t('dashboard.applications')}}</Link>
                <Link v-if="user.role!=='client'" href="/manage/properties" :class="linkClass('/manage/properties')"><DashboardIcon name="home"/>{{t('dashboard.properties')}}</Link>
                <div v-if="user.role==='admin'" class="my-2 border-t border-stone-100"></div>
                <Link v-if="user.role==='admin'" href="/admin/users" :class="linkClass('/admin/users')"><DashboardIcon name="users"/>{{t('dashboard.users')}}</Link>
                <Link v-if="user.role==='admin'" href="/admin/references" :class="linkClass('/admin/references')"><DashboardIcon name="references"/>{{t('dashboard.references')}}</Link>
                <div class="my-2 border-t border-stone-100"></div><Link href="/profile" :class="linkClass('/profile')"><DashboardIcon name="profile"/>{{t('dashboard.profile')}}</Link>
            </nav>
        </aside>
        <div class="min-w-0"><slot/></div>
    </div></AppLayout>
</template>
