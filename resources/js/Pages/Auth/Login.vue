<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useLocale } from '@/composables/useLocale';
defineProps<{ canResetPassword?: boolean; status?: string }>();
const { t } = useLocale();
const form = useForm({ email: '', password: '', remember: false });
const submit = () => form.post(route('login'), { onFinish: () => form.reset('password') });
</script>
<template>
    <GuestLayout>
        <Head :title="t('auth.login')" />
        <div class="eyebrow">NOVA MEMBER SPACE</div>
        <h1 class="mt-3 text-4xl text-[#183c35]">{{ t('auth.welcome') }}</h1>
        <p class="mt-3 text-sm leading-6 text-stone-500">{{ t('auth.subtitle') }}</p>
        <div v-if="status" class="mt-5 rounded-lg bg-emerald-50 p-3 text-sm font-medium text-emerald-700">{{ status }}</div>
        <form class="mt-8 grid gap-5" @submit.prevent="submit">
            <label><span class="label">{{ t('auth.email') }}</span><input v-model="form.email" type="email" class="field !py-3.5" required autofocus autocomplete="username" placeholder="you@example.com"><span class="mt-1 block text-xs text-red-700">{{ form.errors.email }}</span></label>
            <label><span class="label">{{ t('auth.password') }}</span><input v-model="form.password" type="password" class="field !py-3.5" required autocomplete="current-password" placeholder="••••••••"><span class="mt-1 block text-xs text-red-700">{{ form.errors.password }}</span></label>
            <div class="flex items-center justify-between gap-4"><label class="flex items-center gap-2 text-sm text-stone-600"><input v-model="form.remember" type="checkbox" class="rounded border-stone-300 text-[#174c43]">{{ t('auth.remember') }}</label><Link v-if="canResetPassword" :href="route('password.request')" class="text-sm font-bold text-[#174c43]">{{ t('auth.forgot') }}</Link></div>
            <button class="btn btn-primary w-full !py-3.5" :class="{ 'opacity-50': form.processing }" :disabled="form.processing">{{ t('auth.login') }} <span>→</span></button>
        </form>
        <p class="mt-7 text-center text-sm text-stone-500">{{ t('auth.noAccount') }} <Link href="/register" class="font-bold text-[#174c43]">{{ t('auth.register') }}</Link></p>
        <div class="mt-8 rounded-xl border border-stone-200 bg-white p-4 text-xs leading-6 text-stone-500"><strong class="text-stone-700">Demo:</strong> client@nova.test / password</div>
    </GuestLayout>
</template>
