<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import type { PageProps } from '@/types';
import { useLocale, type Locale } from '@/composables/useLocale';

const page = usePage<PageProps>();
const user = computed(() => page.props.auth.user);
const mobile = ref(false);
const languageOpen = ref(false);
const { locale, setLocale, t } = useLocale();
const languages: { code: Locale; label: string }[] = [
    { code: 'en', label: 'EN' }, { code: 'uk', label: 'UA' }, { code: 'ru', label: 'RU' },
];
</script>

<template>
    <div class="flex min-h-screen flex-col">
        <header class="sticky top-0 z-40 border-b border-stone-200 bg-[#fbfaf7]/95 backdrop-blur">
            <div class="container-page flex h-20 items-center justify-between gap-5">
                <Link href="/" class="serif shrink-0 text-2xl font-bold text-[#163f38]">NOVA <span class="font-normal text-[#a67c52]">Estate</span></Link>
                <nav class="hidden items-center gap-7 lg:flex" aria-label="Main navigation">
                    <Link class="nav-link" href="/properties">{{ t('nav.properties') }}</Link>
                    <Link class="nav-link" href="/realtors">{{ t('nav.realtors') }}</Link>
                    <Link class="nav-link" href="/about">{{ t('nav.about') }}</Link>
                    <Link class="nav-link" href="/contacts">{{ t('nav.contacts') }}</Link>
                    <Link class="nav-link" href="/compare">{{ t('nav.compare') }}</Link>
                </nav>
                <div class="hidden items-center gap-3 md:flex">
                    <div class="relative">
                        <button class="flex h-11 items-center gap-2 rounded-lg border border-stone-200 bg-white px-3 text-xs font-extrabold tracking-wider text-[#174c43]" :aria-expanded="languageOpen" @click="languageOpen = !languageOpen">
                            {{ languages.find((item) => item.code === locale)?.label }}
                            <span class="transition-transform" :class="languageOpen ? 'rotate-180' : ''">⌄</span>
                        </button>
                        <div v-if="languageOpen" class="absolute right-0 top-12 z-50 min-w-24 overflow-hidden rounded-xl border bg-white p-1 shadow-xl">
                            <button v-for="language in languages" :key="language.code" class="block w-full rounded-lg px-3 py-2 text-left text-xs font-bold hover:bg-stone-100" :class="locale === language.code ? 'bg-[#e8f0ec] text-[#174c43]' : ''" @click="setLocale(language.code); languageOpen = false">{{ language.label }}</button>
                        </div>
                    </div>
                    <Link v-if="user" href="/dashboard" class="btn btn-light">{{ t('nav.dashboard') }}</Link>
                    <Link v-if="user" href="/logout" method="post" as="button" class="text-sm font-bold text-stone-600">{{ t('nav.logout') }}</Link>
                    <Link v-else href="/login" class="btn btn-primary">{{ t('nav.login') }}</Link>
                </div>
                <button class="p-2 text-2xl md:hidden" aria-label="Open menu" @click="mobile = !mobile">☰</button>
            </div>
            <nav v-if="mobile" class="container-page grid gap-4 border-t py-5 md:hidden">
                <Link href="/properties">{{ t('nav.properties') }}</Link><Link href="/realtors">{{ t('nav.realtors') }}</Link><Link href="/about">{{ t('nav.about') }}</Link><Link href="/contacts">{{ t('nav.contacts') }}</Link>
                <div class="flex gap-2"><button v-for="language in languages" :key="language.code" class="rounded-lg border px-3 py-2 text-xs font-bold" :class="locale === language.code ? 'bg-[#174c43] text-white' : 'bg-white'" @click="setLocale(language.code)">{{ language.label }}</button></div>
                <Link :href="user ? '/dashboard' : '/login'">{{ user ? t('nav.dashboard') : t('nav.login') }}</Link>
            </nav>
        </header>
        <div v-if="page.props.flash?.success" class="fixed right-4 top-24 z-50 rounded-xl bg-[#174c43] px-5 py-4 text-sm font-bold text-white shadow-xl">{{ page.props.flash.success }}</div>
        <main class="flex-1"><slot /></main>
        <footer class="bg-[#102f2a] py-14 text-stone-200">
            <div class="container-page grid gap-10 md:grid-cols-4">
                <div class="md:col-span-2"><div class="serif text-2xl font-bold text-white">NOVA Estate</div><p class="mt-4 max-w-md text-sm leading-7 text-stone-300">{{ t('footer.tagline') }}</p></div>
                <div><div class="mb-4 font-bold text-white">{{ t('footer.navigation') }}</div><div class="grid gap-3 text-sm"><Link href="/properties">{{ t('footer.catalog') }}</Link><Link href="/about">{{ t('footer.company') }}</Link><Link href="/realtors">{{ t('footer.team') }}</Link></div></div>
                <div><div class="mb-4 font-bold text-white">{{ t('footer.contact') }}</div><p class="text-sm leading-7">+1 555 010 20 30<br>hello@nova-estate.test<br>Nova, Peace Avenue, 18</p></div>
            </div>
        </footer>
    </div>
</template>
