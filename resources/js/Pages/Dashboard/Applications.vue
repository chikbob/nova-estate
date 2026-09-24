<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { reactive } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { useLocale } from '@/composables/useLocale';
import type { Paginator, PageProps, Property, User } from '@/types';

type ClientApplication = { id: number; name: string; phone: string; email?: string; message?: string; status: string; created_at: string; property?: Property; realtor?: User; user?: User };
const props = defineProps<{ applications: Paginator<ClientApplication>; realtors: User[]; filters: Record<string, string> }>();
const user = usePage<PageProps>().props.auth.user!;
const { locale, localized, t } = useLocale();
const filterForm = reactive({ search: '', status: '', realtor_id: '', date_from: '', date_to: '', sort: 'newest', ...props.filters });
const statusKeys = ['new', 'in_progress', 'meeting', 'completed', 'cancelled'];
const listUrl = user.role === 'client' ? '/my-applications' : '/manage/applications';
const applyFilters = () => router.get(listUrl, filterForm, { preserveState: true, replace: true });
const resetFilters = () => router.get(listUrl);
const updateStatus = (application: ClientApplication, status: string) => router.patch(`/manage/applications/${application.id}`, { status, realtor_id: application.realtor?.id }, { preserveScroll: true });
const assignRealtor = (application: ClientApplication, realtorId: string) => router.patch(`/manage/applications/${application.id}`, { status: application.status, realtor_id: realtorId || null }, { preserveScroll: true });
const propertyTitle = (property?: Property) => property ? localized(property, 'title', property.title) : t('applications.consultation');
const formatDate = (date: string) => new Intl.DateTimeFormat(locale.value === 'uk' ? 'uk-UA' : locale.value === 'ru' ? 'ru-RU' : 'en-US').format(new Date(date));
</script>

<template>
    <Head :title="t('dashboard.applications')" />
    <DashboardLayout>
        <div class="eyebrow">{{ t('applications.eyebrow') }}</div>
        <h1 class="mt-2 text-4xl">{{ user.role === 'client' ? t('applications.mine') : t('applications.clients') }}</h1>

        <form v-if="user.role !== 'client'" class="mt-7 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm" @submit.prevent="applyFilters">
            <div class="mb-4 flex items-center justify-between gap-4"><h2 class="text-lg">{{ t('applications.filters') }}</h2><button type="button" class="text-xs font-bold text-[#174c43] hover:underline" @click="resetFilters">{{ t('applications.reset') }}</button></div>
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
                <label class="relative md:col-span-2 xl:col-span-2">
                    <span class="sr-only">{{ t('applications.search') }}</span>
                    <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                    <input v-model="filterForm.search" class="field pl-12" :placeholder="t('applications.searchPlaceholder')">
                </label>
                <label><span class="sr-only">{{ t('manage.status') }}</span><select v-model="filterForm.status" class="field"><option value="">{{ t('applications.anyStatus') }}</option><option v-for="status in statusKeys" :key="status" :value="status">{{ t(`applications.status.${status}`) }}</option></select></label>
                <label v-if="user.role === 'admin'"><span class="sr-only">{{ t('applications.agent') }}</span><select v-model="filterForm.realtor_id" class="field"><option value="">{{ t('applications.allAgents') }}</option><option v-for="realtor in realtors" :key="realtor.id" :value="realtor.id">{{ realtor.name }}</option></select></label>
                <label><span class="sr-only">{{ t('applications.dateFrom') }}</span><input v-model="filterForm.date_from" class="field" type="date" :title="t('applications.dateFrom')"></label>
                <label><span class="sr-only">{{ t('applications.dateTo') }}</span><input v-model="filterForm.date_to" class="field" type="date" :title="t('applications.dateTo')"></label>
                <label><span class="sr-only">{{ t('applications.sort') }}</span><select v-model="filterForm.sort" class="field"><option value="newest">{{ t('applications.newest') }}</option><option value="oldest">{{ t('applications.oldest') }}</option><option value="status">{{ t('applications.byStatus') }}</option></select></label>
                <button class="btn btn-primary xl:col-start-6">{{ t('applications.apply') }}</button>
            </div>
        </form>

        <div class="mt-7 grid gap-4">
            <article v-for="application in applications.data" :key="application.id" class="card min-w-0 p-5 sm:p-6">
                <div class="grid min-w-0 gap-5 lg:grid-cols-[minmax(0,1fr)_14rem] lg:items-start">
                    <div class="min-w-0">
                        <span class="status inline-flex max-w-full">{{ t(`applications.status.${application.status}`) }}</span>
                        <h2 class="mt-3 break-words text-2xl leading-tight">{{ propertyTitle(application.property) }}</h2>
                        <p class="mt-2 flex flex-wrap gap-x-2 gap-y-1 text-sm text-stone-500"><span>{{ application.name }}</span><span>· {{ application.phone }}</span><span v-if="application.email">· {{ application.email }}</span><span>· {{ formatDate(application.created_at) }}</span></p>
                        <p v-if="application.message" class="mt-4 break-words text-sm leading-6 text-stone-600">{{ application.message }}</p>
                    </div>
                    <div v-if="user.role !== 'client'" class="grid min-w-0 gap-3">
                        <select class="field min-w-0" :value="application.status" @change="updateStatus(application, ($event.target as HTMLSelectElement).value)"><option v-for="status in statusKeys" :key="status" :value="status">{{ t(`applications.status.${status}`) }}</option></select>
                        <select v-if="user.role === 'admin'" class="field min-w-0" :value="application.realtor?.id || ''" @change="assignRealtor(application, ($event.target as HTMLSelectElement).value)"><option value="">{{ t('applications.unassigned') }}</option><option v-for="realtor in realtors" :key="realtor.id" :value="realtor.id">{{ realtor.name }}</option></select>
                    </div>
                </div>
            </article>
            <div v-if="!applications.data.length" class="card p-12 text-center text-stone-500">{{ t('applications.empty') }}</div>
        </div>
        <Pagination :links="applications.links" />
    </DashboardLayout>
</template>
