<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
defineProps<{status:number}>();
const titles:Record<number,string>={403:'Доступ запрещён',404:'Страница не найдена',500:'Что-то пошло не так'};
const text:Record<number,string>={403:'У вашей учётной записи нет прав для просмотра этой страницы.',404:'Возможно, адрес изменился или страница была удалена.',500:'Мы уже знаем о проблеме. Попробуйте обновить страницу немного позже.'};
</script>
<template><Head :title="titles[status]||'Ошибка'"/><AppLayout><section class="section"><div class="container-page text-center"><div class="serif text-8xl font-bold text-[#174c43]">{{status}}</div><h1 class="mt-5 text-4xl">{{titles[status]||'Ошибка'}}</h1><p class="mx-auto mt-4 max-w-lg text-stone-500">{{text[status]||'Не удалось выполнить запрос.'}}</p><div class="mt-8 flex justify-center gap-3"><Link href="/" class="btn btn-primary">На главную</Link><Link href="/properties" class="btn btn-light">В каталог</Link></div></div></section></AppLayout></template>
