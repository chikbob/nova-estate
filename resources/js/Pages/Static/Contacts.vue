<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useLocale } from '@/composables/useLocale';
const { t } = useLocale();
const form = useForm({ name: '', phone: '', email: '', subject: '', message: '' });
const submit = () => form.post('/contacts', { onSuccess: () => form.reset() });
</script>
<template>
    <Head :title="t('nav.contacts')" />
    <AppLayout><section class="section"><div class="container-page grid gap-12 lg:grid-cols-2">
        <div><div class="eyebrow">{{ t('contacts.eyebrow') }}</div><h1 class="mt-3 text-5xl">{{ t('nav.contacts') }}</h1><p class="prose-copy mt-6 max-w-lg">{{ t('contacts.intro') }}</p><div class="mt-10 grid gap-6"><div><small class="text-stone-500">{{ t('property.phone') }}</small><p class="mt-1 text-xl font-bold">+1 555 010 20 30</p></div><div><small class="text-stone-500">Email</small><p class="mt-1 text-xl font-bold">hello@nova-estate.test</p></div><div><small class="text-stone-500">{{ t('contacts.office') }}</small><p class="mt-1 text-xl font-bold">{{ t('contacts.address') }}</p></div><div><small class="text-stone-500">{{ t('contacts.hours') }}</small><p class="mt-1 text-xl font-bold">{{ t('contacts.schedule') }}</p></div></div></div>
        <form class="card grid gap-5 p-7 md:p-10" @submit.prevent="submit"><label><span class="label">{{ t('property.name') }}</span><input v-model="form.name" class="field" required><span class="text-xs text-red-700">{{ form.errors.name }}</span></label><div class="grid gap-4 sm:grid-cols-2"><label><span class="label">{{ t('property.phone') }}</span><input v-model="form.phone" class="field" required></label><label><span class="label">Email</span><input v-model="form.email" class="field" type="email"></label></div><label><span class="label">{{ t('contacts.subject') }}</span><input v-model="form.subject" class="field"></label><label><span class="label">{{ t('property.message') }}</span><textarea v-model="form.message" class="field" rows="5" /></label><button class="btn btn-primary" :disabled="form.processing">{{ t('contacts.send') }}</button></form>
    </div></section></AppLayout>
</template>
