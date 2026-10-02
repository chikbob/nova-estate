<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useLocale } from '@/composables/useLocale';
import type { User } from '@/types';
defineProps<{ realtors: (User & { properties_count: number })[] }>();
const { t } = useLocale();
const realtorName = (realtor: User) => {
    const key = `realtors.name.${realtor.email}`;
    return t(key) === key ? realtor.name : t(key);
};
</script>
<template>
    <Head :title="t('nav.realtors')" />
    <AppLayout><section class="bg-[#eee9df] py-20"><div class="container-page"><div class="eyebrow">{{ t('realtors.team') }}</div><h1 class="mt-3 text-5xl">{{ t('realtors.hero') }}</h1><p class="prose-copy mt-5 max-w-2xl">{{ t('realtors.intro') }}</p></div></section><section class="section"><div class="container-page grid gap-6 md:grid-cols-2 lg:grid-cols-3"><article v-for="realtor in realtors" :key="realtor.id" class="card p-8"><div class="grid h-20 w-20 place-items-center rounded-full bg-[#e7cda9] text-3xl font-bold text-[#173f38]">{{ realtorName(realtor).charAt(0) }}</div><h2 class="mt-6 text-3xl">{{ realtorName(realtor) }}</h2><p class="prose-copy mt-3 text-sm">{{ t(`realtors.bio.${realtor.email}`) !== `realtors.bio.${realtor.email}` ? t(`realtors.bio.${realtor.email}`) : realtor.bio }}</p><p class="mt-5 text-xs font-bold uppercase tracking-wider text-[#174c43]">{{ realtor.properties_count }} {{ t('realtors.properties') }}</p><Link href="/contacts" class="btn btn-light mt-6">{{ t('realtors.consultation') }}</Link></article></div></section></AppLayout>
</template>
