<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import type { Property } from '@/types';
import { onMounted, ref } from 'vue';
import { useCurrency } from '@/composables/useCurrency';
import { useLocale } from '@/composables/useLocale';

const items = ref<Property[]>([]);
const { t, localized } = useLocale();
const { formatPrice } = useCurrency();
onMounted(() => items.value = JSON.parse(localStorage.getItem('nova_compare') || '[]'));
const remove = (id: number) => {
    items.value = items.value.filter(item => item.id !== id);
    localStorage.setItem('nova_compare', JSON.stringify(items.value));
};
</script>
<template>
    <Head :title="t('compare.title')" />
    <AppLayout><section class="section"><div class="container-page">
        <div class="eyebrow">{{ t('compare.eyebrow') }}</div><h1 class="mt-3 text-5xl">{{ t('compare.title') }}</h1>
        <div v-if="!items.length" class="card mt-10 p-14 text-center"><h2 class="text-3xl">{{ t('compare.empty') }}</h2><p class="mt-3 text-stone-500">{{ t('compare.emptyText') }}</p><Link href="/properties" class="btn btn-primary mt-6">{{ t('compare.openCatalog') }}</Link></div>
        <div v-else class="mt-10 overflow-x-auto"><table class="w-full min-w-[760px] border-collapse bg-white text-left"><thead><tr><th class="border p-4">{{ t('compare.feature') }}</th><th v-for="item in items" :key="item.id" class="border p-4 align-top"><img :src="item.cover_url" :alt="localized(item, 'title', item.title)" class="mb-3 h-32 w-full rounded-lg object-cover"><Link :href="`/properties/${item.slug}`" class="serif text-lg">{{ localized(item, 'title', item.title) }}</Link><button class="mt-2 block text-xs font-bold text-red-700" @click="remove(item.id)">{{ t('compare.remove') }}</button></th></tr></thead><tbody>
            <tr><th class="border bg-stone-50 p-4">{{ t('compare.price') }}</th><td v-for="item in items" :key="item.id" class="border p-4 font-bold text-[#174c43]">{{ formatPrice(item.price) }}</td></tr>
            <tr><th class="border bg-stone-50 p-4">{{ t('compare.operation') }}</th><td v-for="item in items" :key="item.id" class="border p-4">{{ t(item.operation === 'sale' ? 'property.sale' : 'property.rent') }}</td></tr>
            <tr><th class="border bg-stone-50 p-4">{{ t('property.area') }}</th><td v-for="item in items" :key="item.id" class="border p-4">{{ item.area }} m²</td></tr>
            <tr><th class="border bg-stone-50 p-4">{{ t('property.rooms') }}</th><td v-for="item in items" :key="item.id" class="border p-4">{{ item.rooms || '—' }}</td></tr>
            <tr><th class="border bg-stone-50 p-4">{{ t('property.district') }}</th><td v-for="item in items" :key="item.id" class="border p-4">{{ localized(item, 'district', item.district) }}</td></tr>
            <tr><th class="border bg-stone-50 p-4">{{ t('property.floor') }}</th><td v-for="item in items" :key="item.id" class="border p-4">{{ item.floor || '—' }} / {{ item.total_floors || '—' }}</td></tr>
        </tbody></table></div>
    </div></section></AppLayout>
</template>
