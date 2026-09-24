<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { useLocale } from '@/composables/useLocale';
import type { PageProps } from '@/types';

defineProps<{ stats: Record<string, number> }>();
const user = usePage<PageProps>().props.auth.user!;
const { t } = useLocale();
const icons = ['⌂', '♡', '◷', '♙'];
</script>

<template>
    <Head :title="t('dashboard.overview')" />
    <DashboardLayout>
        <section class="relative overflow-hidden rounded-3xl bg-[#174c43] px-7 py-8 text-white shadow-xl shadow-[#174c43]/15 sm:px-9">
            <div class="pointer-events-none absolute -right-12 -top-20 h-64 w-64 rounded-full border-[42px] border-white/5"></div>
            <div class="relative flex flex-wrap items-end justify-between gap-5">
                <div>
                    <div class="text-xs font-bold uppercase tracking-[.2em] text-[#d8c5a9]">{{ t('dashboard.eyebrow') }}</div>
                    <h1 class="mt-3 text-3xl text-white sm:text-4xl">{{ t('dashboard.hello') }}, {{ user.name.split(' ')[0] }}</h1>
                    <p class="mt-2 max-w-xl text-sm leading-6 text-white/65">NOVA Estate · {{ t(`common.${user.role}`) }}</p>
                </div>
                <Link v-if="user.role !== 'client'" href="/manage/properties/create" class="btn bg-white text-[#174c43] hover:bg-[#f2eee5]">+ {{ t('dashboard.newProperty') }}</Link>
            </div>
        </section>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div v-for="(value, label, index) in stats" :key="label" class="group rounded-2xl border border-stone-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="flex items-start justify-between gap-4">
                    <div><div class="text-sm font-medium text-stone-500">{{ label }}</div><div class="serif mt-2 text-4xl font-bold text-[#174c43]">{{ value }}</div></div>
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-[#f2eee5] text-lg text-[#8b6b49]">{{ icons[index % icons.length] }}</span>
                </div>
            </div>
        </div>

        <section class="mt-6 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-7">
            <div class="flex items-center justify-between"><h2 class="text-2xl">{{ t('dashboard.quickActions') }}</h2><span class="text-[#8b6b49]">→</span></div>
            <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <Link href="/properties" class="flex items-center justify-between rounded-xl border border-stone-200 px-4 py-4 font-bold text-stone-700 transition hover:border-[#174c43] hover:bg-[#f7f5ef] hover:text-[#174c43]"><span>⌂ &nbsp;{{ t('dashboard.openCatalog') }}</span><span>→</span></Link>
                <Link v-if="user.role === 'client'" href="/favorites" class="flex items-center justify-between rounded-xl border border-stone-200 px-4 py-4 font-bold text-stone-700 transition hover:border-[#174c43] hover:bg-[#f7f5ef] hover:text-[#174c43]"><span>♡ &nbsp;{{ t('dashboard.myFavorites') }}</span><span>→</span></Link>
                <Link :href="user.role === 'client' ? '/my-applications' : '/manage/applications'" class="flex items-center justify-between rounded-xl border border-stone-200 px-4 py-4 font-bold text-stone-700 transition hover:border-[#174c43] hover:bg-[#f7f5ef] hover:text-[#174c43]"><span>◷ &nbsp;{{ t('dashboard.checkApplications') }}</span><span>→</span></Link>
            </div>
        </section>
    </DashboardLayout>
</template>
