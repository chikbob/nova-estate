<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PropertyCard from '@/Components/PropertyCard.vue';
import Pagination from '@/Components/Pagination.vue';
import { useLocale } from '@/composables/useLocale';
import type { Property } from '@/types';
defineProps<{ properties: { data: Property[]; links: { url: string | null; label: string; active: boolean }[] } }>();
const { t } = useLocale();
</script>
<template>
    <Head :title="t('dashboard.favorites')" />
    <DashboardLayout><div class="eyebrow">{{ t('favorites.eyebrow') }}</div><h1 class="mt-2 text-4xl">{{ t('dashboard.favorites') }}</h1><div v-if="properties.data.length" class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3"><PropertyCard v-for="property in properties.data" :key="property.id" :property="property" favorite /></div><div v-else class="card mt-8 p-12 text-center"><h2 class="text-2xl">{{ t('favorites.empty') }}</h2><Link href="/properties" class="btn btn-primary mt-5">{{ t('dashboard.openCatalog') }}</Link></div><Pagination :links="properties.links" /></DashboardLayout>
</template>
