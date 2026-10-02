<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useLocale } from '@/composables/useLocale';
const props = defineProps<{ status: number }>();
const { t } = useLocale();
const known = computed(() => [403, 404, 500].includes(props.status));
const title = computed(() => t(known.value ? `error.${props.status}.title` : 'error.title'));
const message = computed(() => t(known.value ? `error.${props.status}.text` : 'error.text'));
</script>
<template>
    <Head :title="title" />
    <AppLayout><section class="section"><div class="container-page text-center"><div class="serif text-8xl font-bold text-[#174c43]">{{ status }}</div><h1 class="mt-5 text-4xl">{{ title }}</h1><p class="mx-auto mt-4 max-w-lg text-stone-500">{{ message }}</p><div class="mt-8 flex justify-center gap-3"><Link href="/" class="btn btn-primary">{{ t('auth.backHome') }}</Link><Link href="/properties" class="btn btn-light">{{ t('dashboard.openCatalog') }}</Link></div></div></section></AppLayout>
</template>
