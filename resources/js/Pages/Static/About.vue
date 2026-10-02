<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useLocale } from '@/composables/useLocale';
const { t } = useLocale();
const facts = [['980+', 'about.deals'], ['12', 'about.years'], ['94%', 'about.referred'], ['5', 'about.districts']];
</script>
<template>
    <Head :title="t('nav.about')" />
    <AppLayout>
        <section class="bg-[#173f38] py-20 text-white"><div class="container-page max-w-4xl"><div class="eyebrow !text-[#e7cda9]">NOVA Estate</div><h1 class="mt-4 text-5xl md:text-6xl">{{ t('about.hero') }}</h1><p class="mt-7 max-w-2xl text-lg leading-8 text-stone-300">{{ t('about.intro') }}</p></div></section>
        <section class="section"><div class="container-page grid gap-12 lg:grid-cols-2"><div><h2 class="text-4xl">{{ t('about.trust') }}</h2><p class="prose-copy mt-6">{{ t('about.history') }}</p><p class="prose-copy mt-4">{{ t('about.platform') }}</p></div><div class="grid grid-cols-2 gap-4"><div v-for="[value, label] in facts" :key="label" class="card p-7"><strong class="serif text-4xl text-[#174c43]">{{ value }}</strong><p class="mt-2 text-sm text-stone-500">{{ t(label) }}</p></div></div></div></section>
        <section class="bg-[#eee9df] py-20"><div class="container-page text-center"><h2 class="text-4xl">{{ t('about.ready') }}</h2><p class="mt-4 text-stone-600">{{ t('about.ctaText') }}</p><div class="mt-7 flex justify-center gap-3"><Link href="/properties" class="btn btn-primary">{{ t('dashboard.openCatalog') }}</Link><Link href="/contacts" class="btn btn-light">{{ t('footer.contact') }}</Link></div></div></section>
    </AppLayout>
</template>
