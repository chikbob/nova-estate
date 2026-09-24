<script setup lang="ts">
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { PageProps } from '@/types';
import { useLocale } from '@/composables/useLocale';
defineProps<{ mustVerifyEmail?: boolean; status?: string }>();
const { t } = useLocale();
const user = usePage<PageProps>().props.auth.user!;
const profile = useForm({ name:user.name, email:user.email, phone:user.phone || '', bio:user.bio || '' });
const password = useForm({ current_password:'', password:'', password_confirmation:'' });
const deletion = useForm({ password:'' });
const deleting = ref(false);
const saveProfile = () => profile.patch(route('profile.update'), { preserveScroll:true });
const savePassword = () => password.put(route('password.update'), { preserveScroll:true, onSuccess:()=>password.reset() });
const deleteAccount = () => deletion.delete(route('profile.destroy'), { onSuccess:()=>deleting.value=false });
</script>
<template>
    <Head :title="t('profile.title')" />
    <DashboardLayout>
        <div class="eyebrow">{{ t('profile.eyebrow') }}</div><h1 class="mt-2 text-4xl">{{ t('profile.title') }}</h1><p class="mt-3 max-w-2xl text-sm leading-6 text-stone-500">{{ t('profile.subtitle') }}</p>
        <div class="mt-8 grid gap-6 xl:grid-cols-[1.2fr_.8fr]">
            <section class="card p-7 md:p-9"><div class="flex items-start gap-4"><div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-[#e7cda9] text-xl font-bold text-[#173f38]">{{user.name.charAt(0)}}</div><div><h2 class="text-2xl">{{ t('profile.info') }}</h2><p class="mt-1 text-sm text-stone-500">{{ t('profile.infoText') }}</p></div></div>
                <form class="mt-8 grid gap-5" @submit.prevent="saveProfile"><div class="grid gap-5 md:grid-cols-2"><label><span class="label">{{ t('profile.name') }}</span><input v-model="profile.name" class="field" required><span class="text-xs text-red-700">{{profile.errors.name}}</span></label><label><span class="label">{{ t('auth.email') }}</span><input v-model="profile.email" class="field" type="email" required><span class="text-xs text-red-700">{{profile.errors.email}}</span></label></div><label><span class="label">{{ t('profile.phone') }}</span><input v-model="profile.phone" class="field" type="tel" placeholder="+1 555 000 00 00"></label><label><span class="label">{{ t('profile.bio') }}</span><textarea v-model="profile.bio" class="field" rows="4" maxlength="1000"/></label><div class="flex items-center gap-4"><button class="btn btn-primary" :disabled="profile.processing">{{t('profile.save')}}</button><span v-if="profile.recentlySuccessful" class="text-sm font-bold text-emerald-700">✓ {{t('profile.saved')}}</span></div></form>
            </section>
            <div class="grid gap-6"><section class="card p-7"><h2 class="text-2xl">{{t('profile.security')}}</h2><p class="mt-2 text-sm leading-6 text-stone-500">{{t('profile.passwordText')}}</p><form class="mt-6 grid gap-4" @submit.prevent="savePassword"><label><span class="label">{{t('profile.currentPassword')}}</span><input v-model="password.current_password" class="field" type="password" autocomplete="current-password"><span class="text-xs text-red-700">{{password.errors.current_password}}</span></label><label><span class="label">{{t('profile.newPassword')}}</span><input v-model="password.password" class="field" type="password" autocomplete="new-password"><span class="text-xs text-red-700">{{password.errors.password}}</span></label><label><span class="label">{{t('profile.confirmPassword')}}</span><input v-model="password.password_confirmation" class="field" type="password" autocomplete="new-password"></label><button class="btn btn-light w-full" :disabled="password.processing">{{t('profile.updatePassword')}}</button></form></section>
                <section class="rounded-2xl border border-red-200 bg-red-50/50 p-7"><h2 class="text-2xl text-red-900">{{t('profile.danger')}}</h2><p class="mt-2 text-sm leading-6 text-red-800/70">{{t('profile.dangerText')}}</p><button v-if="!deleting" class="btn btn-danger mt-5" @click="deleting=true">{{t('profile.delete')}}</button><form v-else class="mt-5 grid gap-3" @submit.prevent="deleteAccount"><p class="text-sm text-red-900">{{t('profile.deleteConfirm')}}</p><input v-model="deletion.password" class="field" type="password" :placeholder="t('auth.password')" required><span class="text-xs text-red-700">{{deletion.errors.password}}</span><div class="flex gap-2"><button class="btn btn-danger" :disabled="deletion.processing">{{t('profile.delete')}}</button><button type="button" class="btn btn-light" @click="deleting=false">{{t('profile.cancel')}}</button></div></form></section>
            </div>
        </div>
    </DashboardLayout>
</template>
