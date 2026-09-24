<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useLocale } from '@/composables/useLocale';

defineProps<{ links: { url: string | null; label: string; active: boolean }[] }>();
const { t } = useLocale();
const label = (value: string) => {
    if (value.includes('Previous')) return `‹ ${t('pagination.previous')}`;
    if (value.includes('Next')) return `${t('pagination.next')} ›`;
    return value;
};
</script>

<template>
    <nav class="mt-10 flex flex-wrap justify-center gap-2" :aria-label="t('pagination.label')">
        <template v-for="link in links" :key="link.label">
            <Link
                v-if="link.url"
                :href="link.url"
                preserve-scroll
                class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-xl border px-3.5 py-2 text-center text-sm font-bold transition"
                :class="link.active ? 'border-[#174c43] bg-[#174c43] text-white shadow-md' : 'border-stone-200 bg-white text-stone-700 hover:border-[#174c43] hover:text-[#174c43]'"
            >{{ label(link.label) }}</Link>
            <span
                v-else
                class="inline-flex min-h-11 min-w-11 cursor-not-allowed items-center justify-center rounded-xl border border-stone-200 bg-stone-100 px-3.5 py-2 text-center text-sm font-bold text-stone-400 opacity-70"
                aria-disabled="true"
            >{{ label(link.label) }}</span>
        </template>
    </nav>
</template>
