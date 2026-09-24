<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PropertyCard from '@/Components/PropertyCard.vue';
import Pagination from '@/Components/Pagination.vue';
import type { Paginator, Property, PropertyType } from '@/types';
import { useLocale } from '@/composables/useLocale';
import { useCurrency, type Currency } from '@/composables/useCurrency';

type DistrictOption = { value: string; translations?: PropertyType['translations'] };
const props = defineProps<{ properties: Paginator<Property>; filters: Record<string,string>; types: PropertyType[]; districts: DistrictOption[]; favoriteIds: number[] }>();
const form = reactive({ search:'', operation:'', type:'', price_min:'', price_max:'', rooms:'', area_min:'', district:'', sort:'newest', ...props.filters });
const sortOpen = ref(false);
const { localized, t } = useLocale();
const { currency, setCurrency } = useCurrency();
const currencies: Currency[] = ['USD', 'EUR', 'UAH'];
const apply = () => router.get('/properties', form, { preserveState: true, replace: true });
const reset = () => router.get('/properties');
</script>
<template>
    <Head :title="t('nav.properties')"/><AppLayout>
        <section class="bg-[#173f38] py-16 text-white"><div class="container-page"><div class="eyebrow !text-[#e7cda9]">{{t('catalog.eyebrow')}}</div><h1 class="mt-3 text-5xl">{{t('catalog.title')}}</h1><p class="mt-4 text-stone-300">{{t('catalog.subtitle')}}</p></div></section>
        <section class="section"><div class="container-page grid gap-8 lg:grid-cols-[280px_1fr]">
            <aside><form class="card sticky top-28 grid gap-4 p-5" @submit.prevent="apply"><div class="flex items-center justify-between"><strong>{{t('catalog.filters')}}</strong><button type="button" class="text-xs font-bold text-[#174c43]" @click="reset">{{t('catalog.reset')}}</button></div><label><span class="label">{{t('catalog.search')}}</span><input v-model="form.search" class="field" :placeholder="t('catalog.searchPlaceholder')"></label><label><span class="label">{{t('catalog.operation')}}</span><select v-model="form.operation" class="field"><option value="">{{t('catalog.any')}}</option><option value="sale">{{t('catalog.buy')}}</option><option value="rent">{{t('catalog.rent')}}</option></select></label><label><span class="label">{{t('catalog.type')}}</span><select v-model="form.type" class="field"><option value="">{{t('catalog.any')}}</option><option v-for="propertyType in types" :key="propertyType.id" :value="propertyType.slug">{{ localized(propertyType, 'name', propertyType.name) }}</option></select></label><div class="grid grid-cols-2 gap-2"><label><span class="label">{{t('catalog.priceFrom')}}</span><input v-model="form.price_min" class="field" type="number"></label><label><span class="label">{{t('catalog.to')}}</span><input v-model="form.price_max" class="field" type="number"></label></div><label><span class="label">{{t('catalog.rooms')}}</span><input v-model="form.rooms" class="field" type="number" min="0"></label><label><span class="label">{{t('catalog.area')}}</span><input v-model="form.area_min" class="field" type="number"></label><label><span class="label">{{t('catalog.district')}}</span><select v-model="form.district" class="field"><option value="">{{t('catalog.allDistricts')}}</option><option v-for="district in districts" :key="district.value" :value="district.value">{{ localized(district, 'name', district.value) }}</option></select></label><button class="btn btn-primary">{{t('catalog.show')}}</button></form></aside>
            <div><div class="mb-6 flex flex-wrap items-center justify-between gap-4"><p class="text-sm text-stone-500">{{t('catalog.found')}}: <strong class="text-stone-800">{{ properties.total }}</strong></p><div class="flex items-center gap-3"><div class="flex items-center gap-1 rounded-xl border border-stone-200 bg-white p-1" :aria-label="t('catalog.currency')"><button v-for="code in currencies" :key="code" class="rounded-lg px-3 py-2 text-xs font-extrabold transition" :class="currency===code?'bg-[#174c43] text-white shadow':'text-stone-500 hover:bg-stone-100'" @click="setCurrency(code)">{{code}}</button></div><div class="relative"><select v-model="form.sort" class="field min-w-56 appearance-none bg-white pr-11 font-semibold" @change="apply" @focus="sortOpen=true" @blur="sortOpen=false"><option value="newest">{{t('catalog.newest')}}</option><option value="price_asc">{{t('catalog.priceAsc')}}</option><option value="price_desc">{{t('catalog.priceDesc')}}</option><option value="area_desc">{{t('catalog.areaDesc')}}</option></select><span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-lg text-[#174c43] transition-transform duration-200" :class="sortOpen?'rotate-180':''">⌄</span></div></div></div>
                <div v-if="properties.data.length" class="grid gap-6 md:grid-cols-2 xl:grid-cols-3"><PropertyCard v-for="item in properties.data" :key="item.id" :property="item" :favorite="favoriteIds.includes(item.id)"/></div><div v-else class="card p-14 text-center"><div class="text-5xl">⌕</div><h2 class="mt-5 text-3xl">{{t('catalog.empty')}}</h2><p class="mt-3 text-stone-500">{{t('catalog.emptyText')}}</p><button class="btn btn-primary mt-6" @click="reset">{{t('catalog.reset')}}</button></div><Pagination :links="properties.links"/></div>
        </div></section>
    </AppLayout>
</template>
