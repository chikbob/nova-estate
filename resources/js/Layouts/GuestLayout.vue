<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useLocale, type Locale } from '@/composables/useLocale';
const { locale, setLocale, t } = useLocale();
const languages: {code:Locale; label:string}[] = [{code:'en',label:'EN'},{code:'uk',label:'UA'},{code:'ru',label:'RU'}];
</script>
<template>
    <div class="grid min-h-screen bg-[#f6f4ef] lg:grid-cols-[1.05fr_.95fr]">
        <section class="relative hidden overflow-hidden bg-[#173f38] lg:block">
            <img src="/images/properties/estate-01.jpg" alt="Modern NOVA Estate residence" class="absolute inset-0 h-full w-full object-cover opacity-75">
            <div class="absolute inset-0 bg-gradient-to-t from-[#102f2a] via-[#102f2a]/25 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 p-14 text-white xl:p-20"><div class="eyebrow !text-[#e7cda9]">NOVA Estate</div><h1 class="mt-5 max-w-2xl text-5xl leading-tight">A better way to find your place in the city.</h1><p class="mt-5 max-w-lg text-base leading-7 text-white/75">Curated homes, local expertise and personal support from the first search to the keys.</p></div>
        </section>
        <main class="flex min-h-screen flex-col px-6 py-7 sm:px-12 lg:px-16 xl:px-24">
            <div class="flex items-center justify-between"><Link href="/" class="serif text-2xl font-bold text-[#163f38]">NOVA <span class="font-normal text-[#a67c52]">Estate</span></Link><div class="flex rounded-lg border border-stone-200 bg-white p-1"><button v-for="language in languages" :key="language.code" class="rounded-md px-2.5 py-1.5 text-[11px] font-extrabold" :class="locale===language.code?'bg-[#174c43] text-white':'text-stone-500'" @click="setLocale(language.code)">{{language.label}}</button></div></div>
            <div class="mx-auto flex w-full max-w-md flex-1 items-center py-12"><div class="w-full"><slot /></div></div>
            <Link href="/" class="text-center text-xs font-bold text-stone-500">← {{ t('auth.backHome') }}</Link>
        </main>
    </div>
</template>
