<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { useCurrency } from '@/composables/useCurrency';
import { useLocale } from '@/composables/useLocale';
import type { Paginator, Property } from '@/types';

defineProps<{ properties: Paginator<Property> }>();
const { localized, t } = useLocale();
const { formatPrice } = useCurrency();
const statusClass: Record<string, string> = {
    published: 'bg-emerald-50 text-emerald-700 ring-emerald-600/10',
    moderation: 'bg-amber-50 text-amber-700 ring-amber-600/10',
    draft: 'bg-stone-100 text-stone-600 ring-stone-500/10',
    sold: 'bg-blue-50 text-blue-700 ring-blue-600/10',
    rented: 'bg-violet-50 text-violet-700 ring-violet-600/10',
};
const destroy = (property: Property) => {
    if (confirm(t('manage.deleteConfirm'))) router.delete(`/manage/properties/${property.slug}`);
};
</script>

<template>
    <Head :title="t('dashboard.properties')" />
    <DashboardLayout>
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div class="min-w-0 max-w-3xl"><div class="eyebrow">{{ t('manage.eyebrow') }}</div><h1 class="mt-2 text-4xl">{{ t('manage.title') }}</h1><p class="mt-2 text-sm leading-6 text-stone-500">{{ t('manage.subtitle') }}</p></div>
            <Link href="/manage/properties/create" class="btn btn-primary shrink-0 whitespace-nowrap">+ {{ t('manage.add') }}</Link>
        </div>

        <div class="mt-7 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1080px] table-fixed text-left text-sm">
                    <colgroup><col class="w-[39%]"><col class="w-[14%]"><col class="w-[16%]"><col class="w-[16%]"><col class="w-[15%]"></colgroup>
                    <thead class="bg-[#f7f5ef] text-xs uppercase tracking-[.12em] text-stone-500"><tr><th class="px-5 py-4">{{ t('manage.property') }}</th><th class="px-5 py-4">{{ t('manage.price') }}</th><th class="px-5 py-4">{{ t('manage.status') }}</th><th class="px-5 py-4">{{ t('manage.agent') }}</th><th class="px-5 py-4"></th></tr></thead>
                    <tbody class="divide-y divide-stone-100">
                        <tr v-for="property in properties.data" :key="property.id" class="transition hover:bg-stone-50/70">
                            <td class="px-5 py-4"><div class="flex min-w-0 items-center gap-4"><img :src="property.cover_url" :alt="localized(property, 'title', property.title)" class="h-14 w-20 shrink-0 rounded-xl object-cover" /><div class="min-w-0"><div class="truncate font-bold text-stone-800" :title="localized(property, 'title', property.title)">{{ localized(property, 'title', property.title) }}</div><small class="mt-1 block truncate text-stone-500">{{ localized(property, 'district', property.district) }}</small></div></div></td>
                            <td class="whitespace-nowrap px-5 py-4 font-bold text-[#174c43]">{{ formatPrice(Number(property.price)) }}</td>
                            <td class="px-5 py-4"><span :class="['inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset', statusClass[property.status] ?? statusClass.draft]">{{ t(`manage.${property.status}`) }}</span></td>
                            <td class="truncate px-5 py-4 text-stone-600" :title="property.realtor?.name || t('manage.unassigned')">{{ property.realtor?.name || t('manage.unassigned') }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right"><Link :href="`/manage/properties/${property.slug}/edit`" class="font-bold text-[#174c43] hover:underline">{{ t('manage.edit') }}</Link><button class="ml-4 font-bold text-red-700 hover:underline" @click="destroy(property)">{{ t('manage.delete') }}</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <Pagination :links="properties.links" />
    </DashboardLayout>
</template>
